import { computed, ref, unref, type ComputedRef, type Ref } from 'vue';
import { useHead, useRequestURL, useRuntimeConfig, useSeoMeta } from '#imports';

import type { CompanyInfo } from './useCompanyInfo';
import { useCompanyInfo } from './useCompanyInfo';
import { useApiFetch } from './useApiFetch';

type MaybeRef<T> = T | Ref<T> | ComputedRef<T>;

interface BackendSeo {
  slug: string;
  title: string | null;
  description: string | null;
  canonical_url: string | null;
  meta: Array<BackendSeoMeta>;
  open_graph: {
    title: string | null;
    description: string | null;
    image_url: string | null;
  };
  twitter: {
    title: string | null;
    description: string | null;
    image_url: string | null;
  };
}

interface BackendSeoMeta {
  name?: string | null;
  property?: string | null;
  http_equiv?: string | null;
  content: string | null;
}

export interface SeoMetaTag {
  name?: string | null;
  property?: string | null;
  httpEquiv?: string | null;
  content: string;
}

interface SeoState {
  title: string | null;
  description: string | null;
  canonicalUrl: string | null;
  openGraph: {
    title: string | null;
    description: string | null;
    imageUrl: string | null;
  };
  twitter: {
    title: string | null;
    description: string | null;
    imageUrl: string | null;
    card: string | null;
  };
  extraMeta: SeoMetaTag[];
}

type PartialSeoState = {
  [K in keyof SeoState]?: SeoState[K] extends object ? Partial<SeoState[K]> : SeoState[K];
};

export interface SeoOverrides {
  title?: MaybeRef<string | null | undefined>;
  description?: MaybeRef<string | null | undefined>;
  canonicalUrl?: MaybeRef<string | null | undefined>;
  openGraph?: {
    title?: MaybeRef<string | null | undefined>;
    description?: MaybeRef<string | null | undefined>;
    imageUrl?: MaybeRef<string | null | undefined>;
  };
  twitter?: {
    title?: MaybeRef<string | null | undefined>;
    description?: MaybeRef<string | null | undefined>;
    imageUrl?: MaybeRef<string | null | undefined>;
    card?: MaybeRef<string | null | undefined>;
  };
  extraMeta?: MaybeRef<SeoMetaInput[] | null | undefined>;
}

interface SeoMetaInput {
  name?: MaybeRef<string | null | undefined>;
  property?: MaybeRef<string | null | undefined>;
  httpEquiv?: MaybeRef<string | null | undefined>;
  content?: MaybeRef<string | null | undefined>;
}

export interface UseSeoOptions {
  slug?: MaybeRef<string | null | undefined>;
  overrides?: SeoOverrides;
}

export interface UseSeoApplyOptions {
  replace?: boolean;
}

interface UseSeoReturn {
  backend: Ref<BackendSeo | null>;
  seo: ComputedRef<SeoState>;
  apply: (overrides?: SeoOverrides, options?: UseSeoApplyOptions) => void;
  reset: () => void;
}

export const useSeo = (
  options?: UseSeoOptions | MaybeRef<string | null | undefined>,
  overrides?: SeoOverrides,
): UseSeoReturn => {
  const resolvedOptions = resolveOptions(options, overrides);
  const slugValue = resolveSlug(resolvedOptions.slug);

  const { data: companyInfo } = useCompanyInfo();

  const backendRequest = slugValue
    ? useApiFetch<BackendSeo | null>(`/seo/${encodeURIComponent(slugValue)}`, {
        key: `seo-${slugValue}`,
        server: true,
      })
    : null;

  const backend = backendRequest ? backendRequest.data : ref<BackendSeo | null>(null);

  const runtimeConfig = useRuntimeConfig();
  const requestUrl = useRequestURL();

  const overridesState = ref<PartialSeoState>(normalizeOverrides(resolvedOptions.overrides));
  const siteOrigin = resolveSiteOrigin(runtimeConfig.public, requestUrl);

  const defaults = computed<SeoState>(() =>
    buildDefaultState(
      (companyInfo.value as CompanyInfo | null) ?? null,
      runtimeConfig.public,
      requestUrl,
      siteOrigin,
    ),
  );
  const backendState = computed<PartialSeoState>(() =>
    transformBackendState(backend.value, siteOrigin, requestUrl),
  );
  const finalState = computed<SeoState>(() => {
    const merged = mergeSeoStates(defaults.value, backendState.value, overridesState.value);
    merged.canonicalUrl = normalizeCanonicalUrlValue(merged.canonicalUrl, siteOrigin, requestUrl);

    return merged;
  });

  useSeoMeta({
    title: () => finalState.value.title || undefined,
    description: () => finalState.value.description || undefined,
    ogTitle: () => finalState.value.openGraph.title || finalState.value.title || undefined,
    ogDescription: () => finalState.value.openGraph.description || finalState.value.description || undefined,
    ogImage: () => finalState.value.openGraph.imageUrl || undefined,
    ogUrl: () => finalState.value.canonicalUrl || undefined,
    twitterTitle: () => finalState.value.twitter.title || finalState.value.title || undefined,
    twitterDescription: () => finalState.value.twitter.description || finalState.value.description || undefined,
    twitterImage: () =>
      finalState.value.twitter.imageUrl ||
      finalState.value.openGraph.imageUrl ||
      undefined,
    twitterCard: () => {
      const explicit = finalState.value.twitter.card;
      const allowed: Array<'summary' | 'summary_large_image' | 'app' | 'player'> = [
        'summary',
        'summary_large_image',
        'app',
        'player',
      ];

      if (explicit && allowed.includes(explicit as typeof allowed[number])) {
        return explicit as typeof allowed[number];
      }

      const hasImage =
        Boolean(finalState.value.twitter.imageUrl) ||
        Boolean(finalState.value.openGraph.imageUrl);

      return hasImage ? 'summary_large_image' : 'summary';
    },
  });

  useHead(() => ({
    link: finalState.value.canonicalUrl
      ? [
          {
            rel: 'canonical',
            href: finalState.value.canonicalUrl,
          },
        ]
      : [],
    meta: finalState.value.extraMeta.map((tag) => {
      const payload: Record<string, string> = {
        content: tag.content,
      };

      if (tag.name) {
        payload.name = tag.name;
      }

      if (tag.property) {
        payload.property = tag.property;
      }

      if (tag.httpEquiv) {
        payload['http-equiv'] = tag.httpEquiv;
      }

      return payload;
    }),
  }));

  const applyOverrides = (next?: SeoOverrides, applyOptions?: UseSeoApplyOptions) => {
    if (!next) {
      return;
    }

    const normalized = normalizeOverrides(next);

    overridesState.value = applyOptions?.replace
      ? normalized
      : mergeOverrideStates(overridesState.value, normalized);
  };

  const resetOverrides = () => {
    overridesState.value = {};
  };

  return {
    backend,
    seo: finalState,
    apply: applyOverrides,
    reset: resetOverrides,
  };
};

const resolveOptions = (
  options?: UseSeoOptions | MaybeRef<string | null | undefined>,
  overrides?: SeoOverrides,
): UseSeoOptions => {
  if (isUseSeoOptions(options)) {
    return {
      ...options,
      overrides: overrides ?? options.overrides,
    };
  }

  return {
    slug: options,
    overrides,
  };
};

const isUseSeoOptions = (value: unknown): value is UseSeoOptions => {
  if (!value || typeof value !== 'object') {
    return false;
  }

  if ('value' in value) {
    return false;
  }

  return 'slug' in value || 'overrides' in value;
};

const resolveSlug = (slug?: MaybeRef<string | null | undefined>): string | null => {
  if (slug === undefined) {
    return null;
  }

  const result = resolveMaybeString(slug);

  return result.defined ? result.value : null;
};

const buildDefaultState = (
  companyInfo: CompanyInfo | null,
  publicConfig: Record<string, unknown>,
  requestUrl: URL,
  siteOrigin: string,
): SeoState => {
  const defaultTitle =
    normalizeString(companyInfo?.site_name) ??
    normalizeString(publicConfig.defaultTitle) ??
    'AB Sign Supplies';

  const defaultDescription =
    normalizeString(publicConfig.defaultDescription) ??
    'Your complete source for signage supplies, products, and services.';

  const defaultImage =
    normalizeString(companyInfo?.logo_url) ?? '/favicon.png';
  const canonicalPath = `${requestUrl.pathname}${requestUrl.search ?? ''}` || '/';

  return {
    title: defaultTitle,
    description: defaultDescription,
    canonicalUrl:
      normalizeCanonicalUrlValue(canonicalPath, siteOrigin, requestUrl) ?? requestUrl.href,
    openGraph: {
      title: defaultTitle,
      description: defaultDescription,
      imageUrl: defaultImage,
    },
    twitter: {
      title: defaultTitle,
      description: defaultDescription,
      imageUrl: defaultImage,
      card: defaultImage ? 'summary_large_image' : 'summary',
    },
    extraMeta: [],
  };
};

const transformBackendState = (
  payload: BackendSeo | null,
  siteOrigin: string,
  requestUrl: URL,
): PartialSeoState => {
  if (!payload) {
    return {};
  }

  return {
    title: normalizeString(payload.title),
    description: normalizeString(payload.description),
    canonicalUrl: normalizeCanonicalUrlValue(payload.canonical_url, siteOrigin, requestUrl),
    openGraph: {
      title: normalizeString(payload.open_graph?.title),
      description: normalizeString(payload.open_graph?.description),
      imageUrl: normalizeString(payload.open_graph?.image_url),
    },
    twitter: {
      title: normalizeString(payload.twitter?.title),
      description: normalizeString(payload.twitter?.description),
      imageUrl: normalizeString(payload.twitter?.image_url),
    },
    extraMeta: (payload.meta ?? [])
      .map(normalizeBackendMetaTag)
      .filter((tag): tag is SeoMetaTag => tag !== null),
  };
};

const mergeSeoStates = (
  ...states: Array<PartialSeoState | SeoState | undefined>
): SeoState => {
  const result: SeoState = {
    title: null,
    description: null,
    canonicalUrl: null,
    openGraph: {
      title: null,
      description: null,
      imageUrl: null,
    },
    twitter: {
      title: null,
      description: null,
      imageUrl: null,
      card: null,
    },
    extraMeta: [],
  };

  for (const state of states) {
    if (!state) {
      continue;
    }

    if ('title' in state && state.title !== undefined) {
      result.title = state.title;
    }

    if ('description' in state && state.description !== undefined) {
      result.description = state.description;
    }

    if ('canonicalUrl' in state && state.canonicalUrl !== undefined) {
      result.canonicalUrl = state.canonicalUrl;
    }

    if (state.openGraph) {
      result.openGraph = {
        ...result.openGraph,
        ...state.openGraph,
      };
    }

    if (state.twitter) {
      result.twitter = {
        ...result.twitter,
        ...state.twitter,
      };
    }

    if (state.extraMeta !== undefined) {
      result.extraMeta = mergeMetaTags(result.extraMeta, state.extraMeta as SeoMetaTag[] | undefined);
    }
  }

  return result;
};

const mergeOverrideStates = (
  base: PartialSeoState,
  next: PartialSeoState,
): PartialSeoState => {
  const merged: PartialSeoState = { ...base };

  if ('title' in next) {
    merged.title = next.title ?? null;
  }

  if ('description' in next) {
    merged.description = next.description ?? null;
  }

  if ('canonicalUrl' in next) {
    merged.canonicalUrl = next.canonicalUrl ?? null;
  }

  if (next.openGraph) {
    merged.openGraph = {
      ...(merged.openGraph ?? {}),
      ...next.openGraph,
    };
  }

  if (next.twitter) {
    merged.twitter = {
      ...(merged.twitter ?? {}),
      ...next.twitter,
    };
  }

  if (next.extraMeta) {
    merged.extraMeta = [
      ...(merged.extraMeta ?? []),
      ...next.extraMeta.filter((tag): tag is SeoMetaTag => Boolean(tag)),
    ];
  }

  return merged;
};

const normalizeOverrides = (overrides?: SeoOverrides): PartialSeoState => {
  if (!overrides) {
    return {};
  }

  const normalized: PartialSeoState = {};

  if (hasOwn(overrides, 'title')) {
    const value = resolveMaybeString(overrides.title);

    if (value.defined) {
      normalized.title = value.value;
    }
  }

  if (hasOwn(overrides, 'description')) {
    const value = resolveMaybeString(overrides.description);

    if (value.defined) {
      normalized.description = value.value;
    }
  }

  if (hasOwn(overrides, 'canonicalUrl')) {
    const value = resolveMaybeString(overrides.canonicalUrl);

    if (value.defined) {
      normalized.canonicalUrl = value.value;
    }
  }

  if (overrides.openGraph) {
    const segment: Partial<SeoState['openGraph']> = {};

    if (hasOwn(overrides.openGraph, 'title')) {
      const value = resolveMaybeString(overrides.openGraph.title);

      if (value.defined) {
        segment.title = value.value;
      }
    }

    if (hasOwn(overrides.openGraph, 'description')) {
      const value = resolveMaybeString(overrides.openGraph.description);

      if (value.defined) {
        segment.description = value.value;
      }
    }

    if (hasOwn(overrides.openGraph, 'imageUrl')) {
      const value = resolveMaybeString(overrides.openGraph.imageUrl);

      if (value.defined) {
        segment.imageUrl = value.value;
      }
    }

    if (Object.keys(segment).length > 0) {
      normalized.openGraph = segment;
    }
  }

  if (overrides.twitter) {
    const segment: Partial<SeoState['twitter']> = {};

    if (hasOwn(overrides.twitter, 'title')) {
      const value = resolveMaybeString(overrides.twitter.title);

      if (value.defined) {
        segment.title = value.value;
      }
    }

    if (hasOwn(overrides.twitter, 'description')) {
      const value = resolveMaybeString(overrides.twitter.description);

      if (value.defined) {
        segment.description = value.value;
      }
    }

    if (hasOwn(overrides.twitter, 'imageUrl')) {
      const value = resolveMaybeString(overrides.twitter.imageUrl);

      if (value.defined) {
        segment.imageUrl = value.value;
      }
    }

    if (hasOwn(overrides.twitter, 'card')) {
      const value = resolveMaybeString(overrides.twitter.card);

      if (value.defined) {
        segment.card = value.value;
      }
    }

    if (Object.keys(segment).length > 0) {
      normalized.twitter = segment;
    }
  }

  if (hasOwn(overrides, 'extraMeta')) {
    const extraMeta = resolveMetaInputs(overrides.extraMeta);

    if (extraMeta !== undefined) {
      normalized.extraMeta = extraMeta;
    }
  }

  return normalized;
};

const resolveMetaInputs = (
  input: MaybeRef<SeoMetaInput[] | null | undefined>,
): SeoMetaTag[] | undefined => {
  if (input === undefined) {
    return undefined;
  }

  const value = unref(input);

  if (!value) {
    return [];
  }

  return value
    .map(normalizeOverrideMetaTag)
    .filter((tag): tag is SeoMetaTag => tag !== null);
};

const normalizeOverrideMetaTag = (input?: SeoMetaInput | null): SeoMetaTag | null => {
  if (!input) {
    return null;
  }

  const name = hasOwn(input, 'name') ? resolveMaybeString(input.name).value : null;
  const property = hasOwn(input, 'property') ? resolveMaybeString(input.property).value : null;
  const httpEquiv = hasOwn(input, 'httpEquiv') ? resolveMaybeString(input.httpEquiv).value : null;
  const contentResult = hasOwn(input, 'content') ? resolveMaybeString(input.content) : { defined: false, value: null };

  const content = contentResult.value;

  if (!content) {
    return null;
  }

  if (!name && !property && !httpEquiv) {
    return null;
  }

  return {
    name,
    property,
    httpEquiv,
    content,
  };
};

const normalizeBackendMetaTag = (input?: BackendSeoMeta | null): SeoMetaTag | null => {
  if (!input) {
    return null;
  }

  const content = normalizeString(input.content);

  if (!content) {
    return null;
  }

  const name = normalizeString(input.name);
  const property = normalizeString(input.property);
  const httpEquiv = normalizeString(input.http_equiv);

  if (!name && !property && !httpEquiv) {
    return null;
  }

  return {
    name,
    property,
    httpEquiv,
    content,
  };
};

const mergeMetaTags = (...lists: Array<SeoMetaTag[] | undefined>): SeoMetaTag[] => {
  const entries: SeoMetaTag[] = [];
  const lookup = new Map<string, number>();

  for (const list of lists) {
    if (!list) {
      continue;
    }

    for (const tag of list) {
      if (!tag) {
        continue;
      }

      const key = tag.name
        ? `name:${tag.name}`
        : tag.property
        ? `property:${tag.property}`
        : tag.httpEquiv
        ? `httpEquiv:${tag.httpEquiv}`
        : `content:${tag.content}:${entries.length}`;

      if (lookup.has(key)) {
        entries[lookup.get(key)!] = tag;
      } else {
        lookup.set(key, entries.length);
        entries.push(tag);
      }
    }
  }

  return entries;
};

const resolveSiteOrigin = (publicConfig: Record<string, unknown>, requestUrl: URL): string => {
  const configured = typeof publicConfig.siteUrl === 'string' ? publicConfig.siteUrl.trim() : '';

  if (configured) {
    try {
      const url = new URL(configured);
      return `${url.protocol}//${url.host}`;
    } catch {
      // fall through to request origin
    }
  }

  return requestUrl.origin;
};

const normalizeCanonicalUrlValue = (
  value: string | null,
  siteOrigin: string,
  requestUrl: URL,
): string | null => {
  const normalized = normalizeString(value);

  if (!normalized) {
    return null;
  }

  if (/^https?:\/\//i.test(normalized)) {
    return normalized;
  }

  if (normalized.startsWith('//')) {
    return `${requestUrl.protocol}${normalized}`;
  }

  const base = siteOrigin || requestUrl.origin;
  const path = normalized.startsWith('/') ? normalized : `/${normalized}`;

  try {
    const href = new URL(path, `${base.replace(/\/+$/, '')}/`).href;

    if (path === '/') {
      return href;
    }

    return href.replace(/\/+$/, '');
  } catch {
    return normalized;
  }
};

const resolveMaybeString = (
  input: MaybeRef<string | null | undefined>,
): { defined: boolean; value: string | null } => {
  const raw = unref(input as MaybeRef<string | null | undefined>);

  if (raw === undefined) {
    return { defined: false, value: null };
  }

  if (raw === null) {
    return { defined: true, value: null };
  }

  return {
    defined: true,
    value: normalizeString(raw),
  };
};

const normalizeString = (value: unknown): string | null => {
  if (value === null || value === undefined) {
    return null;
  }

  const stringValue = typeof value === 'string' ? value : String(value);
  const trimmed = stringValue.trim();

  return trimmed === '' ? null : trimmed;
};

const hasOwn = (target: unknown, key: string): boolean => {
  return !!target && Object.prototype.hasOwnProperty.call(target, key);
};

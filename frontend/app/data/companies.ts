export interface CompanyService {
  title: string;
  description: string;
}

export interface CompanyContact {
  phone?: string;
  email?: string;
  address?: string;
  website?: string;
}

export interface CompanyProfile {
  slug: string;
  name: string;
  summary: string;
  overview: string;
  services: CompanyService[];
  contact: CompanyContact;
  logo: string;
  tagline?: string;
}

export const companyProfiles: CompanyProfile[] = [
  {
    slug: 'ab-architectural-graphics',
    name: 'AB Architectural Graphics',
    summary: 'Specialists in architectural wayfinding systems and ADA-compliant signage for healthcare and corporate campuses.',
    overview:
      'AB Architectural Graphics partners with architects and facility planners to produce compliant, beautifully fabricated wayfinding systems. From discovery through installation, our team manages every phase to ensure branding, accessibility, and site logistics are aligned.',
    tagline: 'Human-centered wayfinding from concept to install.',
    services: [
      {
        title: 'Wayfinding Strategy',
        description: 'Needs assessments, nomenclature plans, and sign location schedules tailored to complex campuses.',
      },
      {
        title: 'ADA / Code Compliance',
        description: 'In-house specialists review signage for the latest ADA and local code requirements before fabrication.',
      },
      {
        title: 'Turnkey Project Management',
        description: 'Dedicated project managers coordinate surveys, fabrication, and installation for frictionless rollouts.',
      },
    ],
    contact: {
      phone: '1-800-555-0141',
      email: 'projects@architectural.absigns.com',
      address: '1250 Meridian Way, Suite 300, Dallas, TX 75202',
      website: 'https://example.com/architectural',
    },
    logo: '/images/logo.svg',
  },
  {
    slug: 'ab-lighting-solutions',
    name: 'AB Lighting Solutions',
    summary: 'LED illumination, power supplies, and lighting design consultation for interior and exterior signage.',
    overview:
      'AB Lighting Solutions delivers lighting assemblies that keep signage bright, efficient, and compliant. Our lighting lab qualifies every component for longevity, while our specialists design packages that balance visual impact with energy targets.',
    tagline: 'High-impact illumination engineered for signage.',
    services: [
      {
        title: 'LED System Design',
        description: 'Custom LED layouts, power calculations, and photometric studies for new builds and retrofits.',
      },
      {
        title: 'UL / CSA Certification Support',
        description: 'Pre-certified assemblies and documentation packages to streamline regulatory reviews.',
      },
      {
        title: 'Rapid Fulfillment',
        description: 'Regional warehouses stock LED modules, drivers, and cabling with same-day shipping options.',
      },
    ],
    contact: {
      phone: '1-800-555-0174',
      email: 'support@lighting.absigns.com',
      address: '4800 Lumen Drive, Phoenix, AZ 85004',
      website: 'https://example.com/lighting',
    },
    logo: '/images/logo.svg',
  },
  {
    slug: 'ab-install-services',
    name: 'AB Install Services',
    summary: 'Nationwide installation network providing site surveys, permitting, and field services for retail roll-outs.',
    overview:
      'AB Install Services coordinates national installation campaigns with a vetted partner network. From code research and permits to crane scheduling and final punch lists, our field teams protect timelines and brand standards.',
    tagline: 'Nationwide field services for every roll-out.',
    services: [
      {
        title: 'Site Surveys & Permitting',
        description: 'Local code audits, survey reports, and permit acquisition handled by regional specialists.',
      },
      {
        title: 'Installation Management',
        description: 'Licensed crews, equipment coordination, and safety oversight for single or multi-location programs.',
      },
      {
        title: 'Maintenance Programs',
        description: 'Preventative maintenance and emergency service plans keep signage on-brand year-round.',
      },
    ],
    contact: {
      phone: '1-800-555-0199',
      email: 'dispatch@install.absigns.com',
      address: '675 Fieldstone Avenue, Columbus, OH 43004',
      website: 'https://example.com/install',
    },
    logo: '/logo.svg',
  },
];

export const getCompanyBySlug = (slug: string): CompanyProfile | undefined =>
  companyProfiles.find((company) => company.slug === slug);

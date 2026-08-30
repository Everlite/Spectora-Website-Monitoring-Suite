# Spectora Studio Design System Specification (`DESIGN.md`)

> **Standard:** Generated per `xiaopu-ai/web-design` visual engineering specification  
> **Product:** Spectora — Private Self-Hosted Website Monitoring & Telemetry Suite  
> **Target Audience:** Modern digital agencies, DevOps engineers, and high-end freelancers  
> **Interaction Tier:** L2 (Fluid interactive telemetry, micro-interactions, spring-smoothed metrics)

---

## 1. Visual Theme & Atmosphere

- **Design Philosophy:** Minimalist High-Density Observability (Inspired by Linear, Vercel, and Datadog).
- **Atmosphere:** Deep Matte Charcoal, High Precision Monospace Data, Zero AI-Glow Gimmicks, Crisp Structural Contrast.
- **Visual Rhythm:** 4px/8px metric grid, razor-sharp 1px structural borders, deliberate whitespace hierarchy, instant visual readability from 2 meters away.

---

## 2. Color Palette & Roles

```css
:root {
  /* Canvas & Foundations */
  --studio-bg: #090B10;              /* Main viewport canvas */
  --studio-surface: #10141E;         /* Primary container surfaces */
  --studio-surface-elevated: #161C2A;/* Insets, cards, elevated rows */
  --studio-surface-hover: #1C2436;   /* Active interactive state */
  
  /* Borders & Dividers */
  --studio-border: #1E273A;          /* Structural card borders */
  --studio-border-subtle: #151C2B;   /* Inner dividers & table rows */
  --studio-border-focus: #3B57E8;    /* Active input focus rings */

  /* Primary Brand Accent */
  --studio-brand: #3B57E8;           /* Studio Cobalt (Action buttons, active tabs) */
  --studio-brand-hover: #4F6BFF;     /* Cobalt Hover */
  --studio-brand-subtle: rgba(59, 87, 232, 0.12);

  /* Semantic Observability Tokens */
  --studio-emerald: #10B981;         /* Healthy / Online / 200 OK */
  --studio-emerald-subtle: rgba(16, 185, 129, 0.12);
  --studio-rose: #F43F5E;            /* Outage / Critical Threat / 5xx */
  --studio-rose-subtle: rgba(244, 63, 94, 0.12);
  --studio-amber: #F59E0B;           /* Warning / Expiring SSL / Degraded */
  --studio-amber-subtle: rgba(245, 158, 11, 0.12);
  --studio-cyan: #06B6D4;            /* Telemetry / Pulse Hits */
  
  /* Typography Contrast Hierarchy */
  --studio-text-primary: #F3F4F6;    /* Headings, high-contrast values */
  --studio-text-secondary: #8E98AB;  /* Subtitles, labels, navigation */
  --studio-text-tertiary: #5A667A;   /* Timestamps, meta text, inactive icons */
}
```

---

## 3. Typography Rules

- **Primary Sans Font:** `Plus Jakarta Sans`, `Inter`, -apple-system, sans-serif.
- **Numerical & Telemetry Font:** `JetBrains Mono`, `Fira Code`, monospace.
- **Hierarchy Scale:**
  - Page Titles: `22px` (Bold 800, tracking -0.025em, color `--studio-text-primary`)
  - Section Headers: `14px` (Bold 700, tracking -0.015em, color `--studio-text-primary`)
  - Component Headings / KPI Titles: `11px` (Bold 700, uppercase, tracking +0.05em, color `--studio-text-secondary`)
  - Body Text: `12px` (Medium 500, line-height 1.5, color `--studio-text-primary`)
  - Monospace Data (Latency, SSL, Codes): `11px` / `12px` (Bold 600, color `--studio-text-primary`)
  - Meta & Timestamps: `10px` (Medium 500, color `--studio-text-tertiary`)

---

## 4. Component Stylings

### Cards & Panels
- Background: `--studio-surface` (`#10141E`)
- Border: `1px solid var(--studio-border)` (`#1E273A`)
- Border Radius: `12px`
- Box Shadow: `0 2px 8px rgba(0, 0, 0, 0.35)`

### Buttons
- **Primary:** Background `--studio-brand` (`#3B57E8`), Text White, Radius `8px`, Font Size `12px` (Bold 700), Hover `--studio-brand-hover` (`#4F6BFF`).
- **Secondary:** Background `--studio-surface-elevated` (`#161C2A`), Border `1px solid var(--studio-border)`, Text `--studio-text-primary`, Radius `8px`.
- **Ghost:** Transparent, Text `--studio-text-secondary`, Hover Text White, Hover Background `--studio-surface-elevated`.

### Status Badges & Pills
- **Online Pill:** Background `rgba(16, 185, 129, 0.12)`, Border `1px solid rgba(16, 185, 129, 0.28)`, Text `#10B981`, Dot `#10B981`.
- **Offline Pill:** Background `rgba(244, 63, 94, 0.12)`, Border `1px solid rgba(244, 63, 94, 0.28)`, Text `#F43F5E`, Dot `#F43F5E`.
- **Warning Pill:** Background `rgba(245, 158, 11, 0.12)`, Border `1px solid rgba(245, 158, 11, 0.28)`, Text `#F59E0B`, Dot `#F59E0B`.

---

## 5. Information Architecture & UX Workflow

```
[ Top Utility Bar: Live Fleet Indicator | Target Switcher Dropdown | Quick Search | Push Alerts ]
├── Main Fleet Dashboard (/dashboard)
│   ├── Fleet KPI Deck (Active Targets, SLA Uptime %, Active Incidents, 24h Telemetry)
│   ├── Filter Segment (All, Online, Incidents) + View Switcher (High-Density Table <-> Card Deck)
│   └── Quick Add Target Dialog
└── Unified Domain Observability Cockpit (/domains/{domain})
    ├── Hero Control Header (Live Status, Grade Badge, Instant Probe Trigger, Tracking Code, PDF Export)
    ├── Operational Pillar 1: Availability & SLA Engine (Response Time Sparkline, 30-Day SLA Bar, SSL Counter)
    ├── Operational Pillar 2: Privacy Telemetry & Pulse Deck (Daily Visitors, Device Breakdown, Top Pages, Sources)
    ├── Operational Pillar 3: Heuristic Threat Scanner & Probe Logbook (Watchdog Malware Details, Chronological Event Log)
    └── Operational Pillar 4: Agency Notes & Subpage Monitor (Team Notes stream, Sitemap Auto-Crawler, Monitored Subpages)
```

---

## 6. Do's and Don'ts (Anti-Patterns Guardrails)

- ❌ **DON'T** use multi-color neon halos, glowing radial background blobs, or AI-vibe glass gradients.
- ❌ **DON'T** hide critical monitoring data behind 5 nested tabs; display vital telemetry in a coherent, multi-column dashboard.
- ❌ **DON'T** use browser-default scrollbars or generic Bootstrap/Tailwind standard colors.
- ❌ **DON'T** allow undefined route keys or model attributes to trigger unhandled exceptions.
- ✅ **DO** use monospace fonts (`JetBrains Mono`) for all latencies, timestamps, and status codes.
- ✅ **DO** provide instant 1-click clipboard copy feedback on all code snippets.
- ✅ **DO** implement defensive programming (try-catch, null coalescing, Carbon fallbacks) across all Blade and Controller layers.
- ✅ **DO** ensure the UI is 100% responsive across desktop, tablet, and mobile displays.

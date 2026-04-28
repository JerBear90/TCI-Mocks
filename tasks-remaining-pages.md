# TCI Transportation — Remaining Pages Build Task List

## Overview
Build HTML mockup pages for all remaining URLs from the TCI sitemap (`https://tcitransportation.com/page-sitemap.xml`) that haven't been created yet. Each page follows the existing design system using `tci-all.css`, includes the shared nav/footer, Unsplash images, structured data (JSON-LD), and responsive design.

**Source:** https://tcitransportation.com/page-sitemap.xml
**Design system:** `public_html/tci-all.css`
**Nav template:** `public_html/tci-nav.html`
**Footer template:** `public_html/tci-footer.html`

---

## ✅ Already Completed (60+ pages)

- [x] Homepage (`index.html`)
- [x] Services hub (`services.html`)
- [x] Locations hub (`locations.html`)
- [x] Careers (`careers.html`)
- [x] 26 individual location pages (`locations/*.html`)
- [x] 15 service pages (`services/*.html`)
- [x] 6 dedicated logistics sub-pages (`services/dedicated/*.html`)
- [x] 5 leasing sub-pages (`services/leasing/*.html`)
- [x] 3 brokerage sub-pages (`services/brokerage/*.html`)

---

## 📋 Remaining Pages To Build

### Priority 1: Company Section (8 pages)
These are core brand pages that support trust and authority signals.

- [ ] **Team TCI** — `/team-tci/` → `public_html/team-tci.html`
  - Source: https://tcitransportation.com/team-tci/
  - Content: Company overview, culture, safety, industry leadership, corporate responsibility
  - Schema: Organization

- [ ] **Core Values** — `/core-values/` → `public_html/core-values.html`
  - Source: https://tcitransportation.com/core-values/
  - Content: 5 core value pillars with descriptions
  - Schema: Organization

- [ ] **Leadership** — `/leadership/` → `public_html/leadership.html`
  - Source: https://tcitransportation.com/leadership/
  - Content: Executive team bios with photos (Ryan Flynn, Andrew Flynn, etc.)
  - Schema: Organization + Person

- [ ] **Sustainability** — `/sustainability/` → `public_html/sustainability.html`
  - Source: https://tcitransportation.com/sustainability/
  - Content: GreenShop certification, EV initiatives, environmental practices
  - Schema: Organization

- [ ] **History** — `/history/` → `public_html/history.html`
  - Source: https://tcitransportation.com/history/
  - Content: Company timeline from 1978 to present
  - Schema: Organization

- [ ] **Recognition** — `/recognition/` → `public_html/recognition.html`
  - Source: https://tcitransportation.com/recognition/
  - Content: Awards, certifications, industry rankings
  - Schema: Organization

- [ ] **Reviews** — `/reviews/` → `public_html/reviews.html`
  - Source: https://tcitransportation.com/reviews/
  - Content: Customer testimonials and review aggregation
  - Schema: Organization + Review

- [ ] **Contact** — `/contact/` → `public_html/contact.html`
  - Source: https://tcitransportation.com/contact/
  - Content: Contact form, phone numbers, address, map
  - Schema: Organization + ContactPoint

### Priority 2: Trucks Section (3 pages)
Revenue-driving pages for equipment sales and leasing inventory.

- [ ] **Lease & Rent Inventory** — `/lease-and-rent/` → `public_html/lease-and-rent.html`
  - Source: https://tcitransportation.com/lease-and-rent/
  - Content: Vehicle categories (box trucks, day cabs, sleepers, yard tractors, trailers)
  - Note: Rebuild existing `lease.html` (currently Tailwind) in the shared design system
  - Schema: OfferCatalog

- [ ] **Used Trucks** — `/used-trucks/` → `public_html/used-trucks.html`
  - Source: https://tcitransportation.com/used-trucks/
  - Content: Used truck categories, TCI Certified program, inventory overview
  - Schema: OfferCatalog

- [ ] **Truck & Trailer Parking** — `/truck-trailer-parking/` → `public_html/truck-trailer-parking.html`
  - Source: https://tcitransportation.com/truck-trailer-parking/
  - Content: Parking services, security features, coast-to-coast locations
  - Schema: Service

### Priority 3: Resources & Utility (7 pages)
Supporting pages that improve site authority and user experience.

- [ ] **FAQs** — `/faqs/` → `public_html/faqs.html`
  - Source: https://tcitransportation.com/faqs/
  - Content: Full FAQ page with all categories (dedicated, leasing, maintenance, rental, sales)
  - Schema: FAQPage
  - Note: We already have FAQ content from earlier — expand into full standalone page

- [ ] **24/7 Roadside Assistance** — `/24-7/` → `public_html/24-7.html`
  - Source: https://tcitransportation.com/24-7/
  - Content: Emergency roadside assistance program details
  - Schema: Service

- [ ] **Partners** — `/partners/` → `public_html/partners.html`
  - Source: https://tcitransportation.com/partners/
  - Content: NationaLease, GreenShop, SmartWay, TRALA, CTA, ATA partnerships
  - Schema: Organization

- [ ] **Risk Management** — `/risk-management/` → `public_html/risk-management.html`
  - Source: https://tcitransportation.com/risk-management/
  - Content: Safety programs, insurance, claims process
  - Schema: Service

- [ ] **Forms & Links** — `/forms-links/` → `public_html/forms-links.html`
  - Source: https://tcitransportation.com/forms-links/
  - Content: Downloadable forms and useful links for customers
  - Schema: WebPage

- [ ] **Newsroom** — `/newsroom/` → `public_html/newsroom.html`
  - Source: https://tcitransportation.com/newsroom/
  - Content: Press releases, company news
  - Schema: WebPage

- [ ] **Newsletter Signup** — `/signup/` → `public_html/signup.html`
  - Source: https://tcitransportation.com/signup/
  - Content: Email newsletter subscription form
  - Schema: WebPage

### Priority 4: Landing Pages (5 pages)
Specialized marketing/campaign pages.

- [ ] **Propane Services** — `/propane/` → `public_html/propane.html`
  - Source: https://tcitransportation.com/propane/
  - Content: Propane truck leasing and services
  - Schema: Service

- [ ] **EV Charging Station** — `/ev-charging-station/` → `public_html/ev-charging-station.html`
  - Source: https://tcitransportation.com/ev-charging-station/
  - Content: EV charging infrastructure services
  - Schema: Service

- [ ] **Harvest Season Tractor Rentals** — `/harvest-season-tractor-rentals/` → `public_html/harvest-season-tractor-rentals.html`
  - Source: https://tcitransportation.com/harvest-season-tractor-rentals/
  - Content: Seasonal agricultural tractor rental program
  - Schema: Service

- [ ] **Veterans** — `/veterans/` → `public_html/veterans.html`
  - Source: https://tcitransportation.com/veterans/
  - Content: Veterans hiring program, military transition support
  - Schema: WebPage

- [ ] **Holiday Rentals** — `/holiday-rentals/` → `public_html/holiday-rentals.html`
  - Source: https://tcitransportation.com/holiday-rentals/
  - Content: Seasonal holiday rental program
  - Schema: Service

### Priority 5: Vendor Portal (5 pages)
Internal/vendor-facing pages — lower SEO priority but needed for completeness.

- [ ] **Suppliers** — `/suppliers/` → `public_html/suppliers.html`
  - Source: https://tcitransportation.com/suppliers/
  - Content: Supplier onboarding information
  - Schema: WebPage

- [ ] **Vendor Welcome Email** — `/vendors/welcome-email/` → `public_html/vendors/welcome-email.html`
  - Source: https://tcitransportation.com/vendors/welcome-email/
  - Content: Vendor onboarding welcome page
  - Schema: WebPage

- [ ] **Coupa Registration** — `/vendors/coupa-registration/` → `public_html/vendors/coupa-registration.html`
  - Source: https://tcitransportation.com/vendors/coupa-registration/
  - Content: Coupa supplier portal registration guide
  - Schema: WebPage

- [ ] **Coupa Overview** — `/vendors/coupa-overview/` → `public_html/vendors/coupa-overview.html`
  - Source: https://tcitransportation.com/vendors/coupa-overview/
  - Content: Coupa platform overview for vendors
  - Schema: WebPage

- [ ] **Purchase Order Process** — `/vendors/purchase-order-process/` → `public_html/vendors/purchase-order-process.html`
  - Source: https://tcitransportation.com/vendors/purchase-order-process/
  - Content: PO process documentation for vendors
  - Schema: WebPage

### Skipped (Campaign/Newsletter pages — time-sensitive, low SEO value)
- `/tci-oct25/` — Internal newsletter
- `/december-2025-internal-newsletter/` — Internal newsletter
- `/used-truck-sales-newsletter/` — Campaign page
- `/used-truck-sales-august-2025/` — Campaign page
- `/used-truck-sales-california-truck-show/` — Campaign page
- `/tci-zero-california-truck-show/` — Campaign page
- `/linkedin-core-campaign/` — Campaign page
- `/goodwill-connect-2025/` — Event page
- `/tesla-hvip-grant-submission-form/` — Grant form
- `/tci-isef-hvip-app/` — Grant application
- `/dm/` — Direct mail landing
- `/lp/` — Landing page hub
- `/lp/safety-driven-logistics-partner-with-the-best-in-the-industry/` — Campaign LP
- `/lp/truck-and-trailer-parking/` — Campaign LP
- `/leadership-2/` — Duplicate of `/leadership/`
- `/test/` — Test page
- `/privacy-policy/` — Legal (keep existing WP page)
- `/file-a-claim/` — Form page
- `/report-a-driving-incident/` — Form page

---

## Build Specifications (for each page)

1. **Fetch content** from the live TCI URL
2. **Use the shared design system** — link to `tci-all.css`, include nav/footer
3. **Responsive** — must work at 1100px, 900px, and 767px breakpoints
4. **SEO** — unique H1, meta description, JSON-LD structured data, BreadcrumbList
5. **Images** — use Unsplash with descriptive filenames (download to `wp-uploads-2026-04/`)
6. **No keyword cannibalization** — each page targets unique keywords
7. **Copy doc** — add corresponding markdown file to `copy-docs/` for client review
8. **Nav links** — update `tci-nav.html` if new nav items are needed (Company dropdown, Trucks dropdown)
9. **Git** — commit and push after each priority group

---

## Total: 28 remaining pages across 5 priority groups

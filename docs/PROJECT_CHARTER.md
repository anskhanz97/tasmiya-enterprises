# TASMIYA ENTERPRISE - Master Requirements Document

**Version:** 1.0  
**Date:** February 4, 2026  
**Project Type:** Laravel-based Multi-Sector Enterprise Web Application  
**Status:** Development Phase 1 - Setup & Planning

---

## 1. PROJECT OVERVIEW

**Project Name:** TASMIYA ENTERPRISE  
**Description:** A sophisticated multi-sector web application representing a professional enterprise with three specialized business divisions operating under one unified brand.

**Business Model:** Multi-sector service provider offering FBR taxation services, IT/digital marketing services, and technical/physical support services.

**Development Approach:** Learn-through-building with comprehensive documentation explaining architecture, design decisions, and implementation patterns at each phase.

---

## 2. BUSINESS STRUCTURE & ORGANIZATIONAL HIERARCHY

### Organization Chart
```
TASMIYA ENTERPRISE (Parent Company)
│
├── Division 1: FBR Taxation Services
│   └── Lead: Atif Safdar
│       Tagline: "Let's Be Money Smart"
│       Services: Filing, Sale Tax, GSTs, Property Tax, General Tax
│       CTA: "GET FILER NOW" / "Let's Have a Discussion"
│
├── Division 2: IT Services & Digital Marketing
│   ├── Lead: Waseem Asghar
│   ├── Lead: Ans Khan
│       Tagline: "If you can think it, we can build it"
│       Services: Web Apps, Website Development, Web Design, Social Media Marketing, 
│                 Account Management, Brand Creation, AI Graphic Design, AI Video Gen
│       CTA: "GET a Quote NOW" / "Let's Discuss your Idea"
│
└── Division 3: Technical/Physical Support & Installation
    └── Lead: Nazim Rauf
        Tagline: "Let Technology do the Work for you"
        Services: Installation, Configuration, Maintenance
        Products: Cameras, Network, DVR/NVR, Door Locks, Sensors, Alarms,
                 Electric Wiring, Facial Recognition, Automatic Doors, Solar Systems
        CTA: "Want a Quotation?" / "Hire a Professional!"
        Client Base: Homes, Societies, Firms, Factories, Offices, Clubs, Gyms
        Brands: HikVision, ZKT, etc.
```

### Team Members (4)
1. **Atif Safdar** - Division 1 Lead (Tax/Legal Services)
2. **Waseem Asghar** - Division 2 Lead (IT/Digital Marketing)
3. **Ans Khan** - Division 2 Co-Lead (IT/Digital Marketing)
4. **Nazim Rauf** - Division 3 Lead (Technical Support)

---

## 3. TECHNOLOGY STACK

| Component | Technology | Version |
|-----------|-----------|---------|
| **Backend** | Laravel | 11.x (LTS) |
| **Language** | PHP | 8.2+ |
| **Frontend** | HTML5, CSS3, JavaScript (ES6+) | Latest |
| **Database** | MySQL | 8.0+ |
| **Local Testing** | XAMPP | Latest |
| **Version Control** | Git | 2.x |
| **Package Manager** | Composer | Latest |
| **Server** | Apache (XAMPP) | Latest |

---

## 4. KEY FEATURES TO IMPLEMENT

### Authentication & Authorization
- [ ] Secure admin login system (email/password)
- [ ] Team member authentication (individual accounts)
- [ ] Role-based access control (Admin, Team Member, Guest)
- [ ] Password reset functionality
- [ ] Session management and security

### Team & Profile Management
- [ ] Individual profile pages for each team member
- [ ] Division-specific page designs with unique theming
- [ ] Profile information management (admin + self-edit)
- [ ] Photo galleries with Google Drive integration
- [ ] Contact information display (email, phone, WhatsApp)

### Content Pages
- [ ] Home page with hero section and team showcase
- [ ] Individual services pages (one per division)
- [ ] Team member profile pages (4 separate pages)
- [ ] Projects/Portfolio showcase page
- [ ] Testimonials/Reviews section
- [ ] Contact form with inquiry routing

### Advanced Features
- [ ] Google Drive API integration for image uploads
- [ ] Payment gateway integration (Visa/MasterCard)
- [ ] WhatsApp Business API integration
- [ ] Email notification system
- [ ] RESTful API endpoints
- [ ] Webhooks for event handling
- [ ] WebSocket implementation for real-time features

### Admin Features
- [ ] Content management dashboard
- [ ] User management system
- [ ] Analytics and reporting
- [ ] Payment management
- [ ] Email template management

---

## 5. VISUAL DESIGN STRATEGY - DIVISION-SPECIFIC THEMING

### Option A: CSS Variables + Distinct Imagery (SELECTED)

#### Division 1: FBR Taxation Services (Atif Safdar)
- **Color Scheme:** Professional blues, grays, golds
- **Imagery:** Legal documents, tax forms, financial charts, professional office settings
- **Typography:** Serif fonts for authority, sans-serif for readability
- **Icons:** Briefcase, document, calculator, shield symbols
- **Mood:** Professional, trustworthy, authoritative

#### Division 2: IT Services & Digital Marketing (Waseem & Ans)
- **Color Scheme:** Tech colors - purples, cyans, neon accents
- **Imagery:** Code, digital devices, creative tools, modern interfaces
- **Typography:** Modern sans-serif with geometric feel
- **Icons:** Code brackets, gears, rocket, lightbulb
- **Mood:** Innovative, creative, forward-thinking, tech-savvy

#### Division 3: Technical Support & Installation (Nazim Rauf)
- **Color Scheme:** Industrial colors - oranges, dark grays, steel blues
- **Imagery:** Installation work, security equipment, networks, tools, completed projects
- **Typography:** Bold sans-serif for clarity
- **Icons:** Tools, network, camera, lock, settings
- **Mood:** Practical, reliable, professional, trustworthy, hardworking

### CSS Implementation
```
Root CSS Variables per Division:
--primary-color
--secondary-color
--accent-color
--text-color
--background-color
--success-color
--warning-color
--error-color
```

---

## 6. GIT WORKFLOW (SIMPLIFIED)

```
main branch (production-ready code)
  │
  └── feature branch (development)
       │
       └── Merge back to main after each phase completion
```

**Guidelines:**
- `main`: Stable, tested, working code only
- `feature`: Active development, experimental code
- Merge to `main` after phase completion and testing

---

## 7. DEVELOPMENT PHASES & TIMELINE

### Phase 1: Planning & Environment Setup (Week 1-2)
- Initialize Git repository
- Set up Laravel project structure
- Configure XAMPP and MySQL
- Create database schema
- Write Phase 1 documentation
- **Deliverable:** Working Laravel project, database setup, initial documentation

### Phase 2: Authentication & Authorization (Week 3)
- Implement Laravel Auth system
- Create admin login/register
- Build team member accounts
- Implement role-based middleware
- **Deliverable:** Working authentication system

### Phase 3: Team Profiles & Theming (Week 4)
- Create Profile models and controllers
- Implement division-specific theming
- Build profile management dashboard
- Google Drive integration setup
- **Deliverable:** Functional profile system with theming

### Phase 4: Frontend Development (Week 5-6)
- Design and build home page
- Create individual service pages
- Build team member profile pages
- Implement responsive design
- **Deliverable:** Fully responsive frontend

### Phase 5: Integrations - Payment & WhatsApp (Week 7)
- Integrate payment gateway
- Implement WhatsApp API
- Create payment workflows
- **Deliverable:** Working payment and messaging systems

### Phase 6: Real-time Features (Week 8)
- Set up WebSockets
- Implement webhooks
- Build notification system
- **Deliverable:** Real-time notification system

### Phase 7: APIs & Testing (Week 9-10)
- Develop RESTful APIs
- Create comprehensive tests
- Document API endpoints
- **Deliverable:** Complete API with documentation and tests

### Phase 8: Email & Notifications (Week 11)
- Implement email system
- Create email templates
- Test notification workflows
- **Deliverable:** Working email notification system

### Phase 9: Deployment & Optimization (Week 12-14)
- Optimize database queries
- Implement caching strategies
- Deploy to live server
- Create deployment documentation
- **Deliverable:** Live, optimized application

---

## 8. USER ROLES & PERMISSIONS MATRIX

### Admin Role
```
✓ Full system access
✓ User management (CRUD all users)
✓ Content management (CRUD all content)
✓ Payment management and refunds
✓ Analytics and reporting
✓ System configuration
✓ View activity logs
```

### Team Member Role
```
✓ Login to dashboard
✓ View own profile
✓ Edit own profile (name, bio, photo via Google Drive)
✓ View own inquiries
✓ Respond to inquiries
✓ View own analytics
✗ Access other members' sensitive data
✗ Access admin functions
```

### Guest/Visitor Role
```
✓ View all public pages
✓ View team member profiles
✓ View services
✓ View portfolio
✓ Submit contact form
✗ Access authentication pages (hidden)
✗ Access admin/member dashboards
```

---

## 9. DATABASE ENTITIES (High Level)

```
Users
├── id
├── email
├── password
├── role (admin, team_member, guest)
├── created_at
└── updated_at

TeamMembers/Profiles
├── id
├── user_id (FK)
├── division_id (FK)
├── full_name
├── bio
├── specialization
├── phone
├── whatsapp_number
├── photo_url (Google Drive)
├── social_links
└── timestamps

Divisions
├── id
├── name
├── description
├── primary_color
├── secondary_color
└── timestamps

Projects
├── id
├── team_member_id (FK)
├── title
├── description
├── images
├── client_name
├── completion_date
└── timestamps

Testimonials
├── id
├── team_member_id (FK)
├── client_name
├── rating
├── review_text
├── image_url
└── timestamps

Payments
├── id
├── user_id (FK)
├── amount
├── status
├── transaction_id
├── payment_method
└── timestamps

ContactInquiries
├── id
├── name
├── email
├── phone
├── division_id (FK)
├── message
├── status
└── timestamps
```

---

## 10. LEARNING OBJECTIVES

### Laravel Mastery
- [x] Project initialization and structure
- [ ] Authentication and authorization systems
- [ ] Eloquent ORM and relationships
- [ ] Database migrations and seeding
- [ ] Controllers and routes (RESTful)
- [ ] Middleware and request lifecycle
- [ ] Email notifications
- [ ] Job queues (for async tasks)
- [ ] API development with proper response formatting
- [ ] Webhooks and event handling
- [ ] Broadcasting and WebSockets
- [ ] Testing with PHPUnit

### Integration Learning
- [ ] Google Drive API integration
- [ ] Payment gateway integration (Stripe/Similar)
- [ ] WhatsApp Business API
- [ ] OAuth implementation
- [ ] Webhook creation and handling

### Best Practices
- [ ] SOLID principles
- [ ] Design patterns
- [ ] Security best practices
- [ ] Performance optimization
- [ ] Code organization and maintainability

### Frontend Skills
- [ ] Responsive design (mobile-first)
- [ ] CSS theming and variables
- [ ] JavaScript async operations
- [ ] AJAX and API consumption
- [ ] Accessibility standards

---

## 11. PROJECT STRUCTURE

```
TamiyaEnterprises/
├── .git/                          # Git repository
├── app/                           # Laravel application code
│   ├── Models/                    # Eloquent models
│   ├── Controllers/               # Request handlers
│   ├── Middleware/                # Custom middleware
│   ├── Jobs/                      # Queue jobs
│   └── Events/                    # Event classes
├── resources/
│   ├── views/                     # Blade templates
│   │   ├── layouts/               # Shared layouts
│   │   ├── divisions/             # Division-specific pages
│   │   ├── profiles/              # Profile pages
│   │   └── components/            # Reusable components
│   ├── css/                       # Stylesheets
│   │   ├── main.css               # Global styles
│   │   ├── variables.css          # CSS variables per division
│   │   ├── division1.css           # Tax division theme
│   │   ├── division2.css           # IT division theme
│   │   └── division3.css           # Technical support theme
│   └── js/                        # JavaScript files
├── routes/
│   ├── web.php                    # Web routes
│   ├── api.php                    # API routes
│   └── admin.php                  # Admin routes
├── database/
│   ├── migrations/                # Database migrations
│   ├── seeders/                   # Database seeders
│   └── schema.sql                 # Database schema documentation
├── config/                        # Configuration files
├── docs/                          # Documentation
│   ├── PROJECT_CHARTER.md         # This file
│   ├── PHASE_1_SETUP.md           # Phase 1 guide
│   ├── PHASE_2_AUTH.md            # Phase 2 guide
│   ├── ARCHITECTURE.md            # System architecture
│   ├── DATABASE_SCHEMA.md         # Database documentation
│   ├── API_DOCUMENTATION.md       # API reference
│   ├── LEARNING_PATH.md           # Learning guide
│   ├── DEPLOYMENT_GUIDE.md        # Deployment instructions
│   └── MAINTENANCE.md             # Maintenance guide
├── tests/                         # Test files
├── public/                        # Public assets
├── storage/                       # Temporary storage
├── .env.example                   # Environment variables template
├── .gitignore                     # Git ignore file
├── composer.json                  # PHP dependencies
└── README.md                      # Project README

```

---

## 12. SUCCESS CRITERIA

- ✓ All functional requirements implemented
- ✓ Application performs with < 3 seconds page load time
- ✓ All security vulnerabilities addressed
- ✓ Comprehensive documentation completed
- ✓ Educational materials provide clear learning path
- ✓ Code quality meets Laravel best practices
- ✓ Division-specific theming visually distinct
- ✓ Team members can self-manage profiles
- ✓ Payment processing works securely
- ✓ All integrations (Google Drive, WhatsApp) functional
- ✓ Responsive design on all devices
- ✓ 80%+ test coverage for critical features

---

## 13. ASSUMPTIONS & CONSTRAINTS

### Assumptions
- XAMPP available for local development
- MySQL available for database
- Team members have Google accounts for Drive integration
- Clients accept Visa/MasterCard payments
- Modern browsers (Chrome, Firefox, Safari, Edge) supported

### Constraints
- Solo development (no team)
- Simple Git workflow (main + feature branches only)
- Open-source libraries preferred
- 14-week development timeline
- Data privacy compliance required

---

## 14. NEXT STEPS

**Immediate Actions:**
1. Install XAMPP (if not already installed)
2. Install Composer (PHP package manager)
3. Create fresh Laravel project using `composer create-project laravel/laravel tasmiya-enterprise`
4. Configure XAMPP with Laravel project
5. Set up MySQL database
6. Begin Phase 1 implementation

**Documentation to Create:**
- [ ] PHASE_1_SETUP.md - Detailed setup instructions
- [ ] ARCHITECTURE.md - System architecture and design decisions
- [ ] DATABASE_SCHEMA.md - ER diagrams and schema documentation

---

**Document Status:** Ready for Review  
**Last Updated:** February 4, 2026  
**Next Phase:** Phase 1 - Environment Setup & Laravel Installation

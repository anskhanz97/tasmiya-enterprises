# SYSTEM ARCHITECTURE - TASMIYA ENTERPRISE

**Version:** 1.0  
**Date:** February 4, 2026  
**Purpose:** Document system design, component interactions, and design decisions

---

## 1. HIGH-LEVEL SYSTEM ARCHITECTURE

### System Overview Diagram

```
┌─────────────────────────────────────────────────────────────┐
│                      CLIENT (Browser)                        │
│  ┌────────────┐  ┌─────────────────┐  ┌──────────────────┐ │
│  │  HTML/CSS  │  │  JavaScript     │  │  WebSockets      │ │
│  │   Pages    │  │  (AJAX Calls)   │  │  (Real-time)     │ │
│  └────────────┘  └─────────────────┘  └──────────────────┘ │
└─────────────────────┬──────────────────────────────────────┘
                      │ HTTPS Requests
                      ▼
┌─────────────────────────────────────────────────────────────┐
│              LARAVEL WEB APPLICATION                         │
│  ┌──────────────┐  ┌────────────────┐  ┌──────────────────┐│
│  │   Routes     │  │  Middleware    │  │  Controllers     ││
│  │ (URL Maps)   │  │ (Auth, CORS)   │  │ (Request Logic)  ││
│  └──────────────┘  └────────────────┘  └──────────────────┘│
│  ┌──────────────┐  ┌────────────────┐  ┌──────────────────┐│
│  │   Models     │  │  Blade Views   │  │  Validation      ││
│  │ (Database)   │  │ (Templates)    │  │ (Input Checks)   ││
│  └──────────────┘  └────────────────┘  └──────────────────┘│
│  ┌──────────────┐  ┌────────────────┐  ┌──────────────────┐│
│  │  Services    │  │  Jobs/Queue    │  │  Events/WebSock  ││
│  │ (Business)   │  │  (Background)  │  │  (Real-time)     ││
│  └──────────────┘  └────────────────┘  └──────────────────┘│
└─────────┬──────────────────┬─────────────────────────┬──────┘
          │                  │                         │
    Data │             Storage│                    Cache│
          ▼                  ▼                         ▼
    ┌─────────┐      ┌──────────────┐      ┌──────────────┐
    │ MySQL   │      │ Google Drive  │      │ File Cache   │
    │Database │      │ (Images)      │      │ (Laravel)    │
    └─────────┘      └──────────────┘      └──────────────┘

External Integrations:
┌──────────────┐  ┌──────────────┐  ┌──────────────────────┐
│ Stripe/Payment│ │ WhatsApp API  │  │ Email Service        │
│   Gateway    │ │  (Messaging)  │  │ (Mailtrap/SendGrid)  │
└──────────────┘  └──────────────┘  └──────────────────────┘
```

---

## 2. DESIGN PATTERNS USED

### 2.1 MVC (Model-View-Controller)

**Components:**

```
Model ─────→ Controller ←───── View
  ↑            ↕               ↓
  │         Request        Response
  └──────────────────────────────┘
       (Database Query)
```

**Example Flow: User Views Team Member Profile**

```
1. User clicks "Atif Safdar" link → URL: /profiles/atif-safdar
2. Route maps to ProfileController@show
3. Controller queries: Profile::where('slug', 'atif-safdar')->first()
4. Model returns Profile data from database
5. Controller passes data to View
6. View (Blade template) renders HTML with profile data
7. Browser displays formatted profile page
```

### 2.2 Repository Pattern (Future Implementation)

**Purpose:** Abstraction layer between controllers and database

**Benefits:**
- Easier to test (can mock repository)
- Database changes don't affect controllers
- Reusable query logic

**Example:**
```php
// Instead of querying directly in controller:
$profile = Profile::where('slug', $slug)->first();

// Use repository:
$profile = $profileRepository->getBySlug($slug);
// Repository handles all database logic
```

### 2.3 Service Layer Pattern

**Purpose:** Encapsulate business logic outside controllers

**Example:**
```php
// Controller (thin)
public function store(Request $request) {
    $payment = $paymentService->processPayment($request->all());
    return redirect()->route('payments.success');
}

// Service (thick with business logic)
class PaymentService {
    public function processPayment($data) {
        $validated = $this->validatePayment($data);
        $transaction = $this->stripe->charge($validated);
        $this->logPayment($transaction);
        $this->sendNotification($transaction);
        return $transaction;
    }
}
```

### 2.4 Middleware Pipeline

**Purpose:** Filter requests before reaching controller

**Diagram:**
```
Request
  ↓
[CORS Middleware] ─→ Check cross-origin access
  ↓
[Auth Middleware] ─→ Verify user is logged in
  ↓
[Admin Middleware] ─→ Verify user is admin
  ↓
Controller ─→ (Middleware passes if all checks succeed)
```

**Example Stack:**
```php
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/dashboard', 'AdminController@dashboard');
});
```

---

## 3. LARAVEL REQUEST LIFECYCLE

**Detailed request flow:**

```
1. User makes HTTP request (clicks link, submits form)
   ↓
2. Request hits public/index.php (Laravel entry point)
   ↓
3. ServiceProvider boots application
   ↓
4. Request enters HTTP Kernel
   ↓
5. Middleware Pipeline processes request:
   - Auth middleware checks login
   - CORS middleware checks origin
   - Custom middleware (role checks, etc)
   ↓
6. Router matches request to route/controller
   ↓
7. Route Model Binding (if using implicit binding)
   ↓
8. Controller method executed
   ↓
9. Controller returns Response (View/JSON)
   ↓
10. Response goes through middleware (reverse order)
    ↓
11. Response sent to client/browser
    ↓
12. Browser renders HTML or processes JSON
```

---

## 4. DATABASE ARCHITECTURE

### 4.1 ER Diagram (Entity Relationship)

```
┌──────────────┐         ┌──────────────┐
│    Users     │         │  Divisions   │
├──────────────┤         ├──────────────┤
│ id (PK)      │         │ id (PK)      │
│ email        │         │ name         │
│ password     │         │ description  │
│ role         │◄────┐   │ color_primary│
│ created_at   │     │   │ created_at   │
└──────────────┘     │   └──────────────┘
      │              │
      │              ▲
      │              │ (has)
      │              │
      │        ┌─────────────────┐
      │        │ TeamMembers     │
      │        ├─────────────────┤
      │        │ id (PK)         │
      │        │ user_id (FK)    │◄─────┐
      │        │ division_id (FK)│      │
      │        │ full_name       │      │
      │        │ phone           │      │
      │        │ created_at      │      │
      │        └─────────────────┘      │
      │              │                  │
      │              │ (has many)       │
      │              ▼                  │
      │        ┌─────────────────┐      │
      │        │ Projects        │      │
      │        ├─────────────────┤      │
      │        │ id (PK)         │      │
      │        │ team_member_id  │──────┘
      │        │ title           │
      │        │ description     │
      │        │ created_at      │
      │        └─────────────────┘
      │
      │        ┌─────────────────┐
      │        │ Testimonials    │
      │        ├─────────────────┤
      │        │ id (PK)         │
      │        │ team_member_id  │──────┐
      │        │ client_name     │      │
      │        │ rating          │      │
      │        │ review_text     │      │
      │        │ created_at      │      │
      │        └─────────────────┘      │
      │                                 │
      │        ┌─────────────────┐      │
      │        │ ContactInquiries│      │
      │        ├─────────────────┤      │
      │        │ id (PK)         │      │
      │        │ division_id     │      │
      │        │ name            │      │
      │        │ email           │      │
      │        │ message         │      │
      │        │ status          │      │
      │        │ created_at      │      │
      │        └─────────────────┘      │
      │                                 │
      │        ┌─────────────────┐      │
      │        │ Payments        │      │
      │        ├─────────────────┤      │
      │        │ id (PK)         │      │
      │        │ user_id (FK)    │──────┘
      │        │ amount          │
      │        │ status          │
      │        │ transaction_id  │
      │        │ created_at      │
      │        └─────────────────┘
      │
      └─→ (Relationships: User can be Admin managing system)
```

### 4.2 Table Relationships Explained

**One-to-Many Relationships:**

```
Division (1) ─────────────→ (Many) TeamMembers
  Example: FBR division has member "Atif Safdar"

TeamMember (1) ─────→ (Many) Projects
  Example: Waseem Asghar has 10+ completed projects

TeamMember (1) ─────→ (Many) Testimonials
  Example: Nazim Rauf has 20+ client reviews

User (1) ─────→ (Many) Payments
  Example: One user can make multiple payments
```

**Many-to-One Relationships:**

```
Projects ─────→ TeamMember
  Many projects belong to one team member

Testimonials ─────→ TeamMember
  Many testimonials about one team member

Payments ─────→ User
  Many payments made by one user
```

### 4.3 Database Normalization

**Normalization Principles:**

1. **First Normal Form (1NF):** Each cell contains single value
2. **Second Normal Form (2NF):** No partial dependencies
3. **Third Normal Form (3NF):** No transitive dependencies

**Our Database follows 3NF:**

❌ Wrong (violates normalization):
```
TeamMembers table:
| id | name | phone | division_name | division_color |
|  1 | Atif |  ... | FBR Taxation  | blue           |
```
Problem: Division info repeated for each member

✓ Correct (normalized):
```
Divisions table:
| id | name | color |
|  1 | FBR  | blue  |

TeamMembers table:
| id | name | phone | division_id |
|  1 | Atif | ...   | 1           |
```
Benefit: Change division color once, affects all members

---

## 5. AUTHENTICATION & AUTHORIZATION ARCHITECTURE

### 5.1 Authentication Flow

```
User Login Request
        ↓
  [Validate Email/Password]
        ↓
  [Password correct?] ─NO→ Show error, redirect to login
        ↓ YES
  [Create Session/Token]
        ↓
  [Generate Auth Cookie]
        ↓
  Redirect to Dashboard
        ↓
[Session stored in database/cache]
        ↓
Subsequent requests include session cookie
        ↓
Auth Middleware verifies session
        ↓
✓ Access granted OR ✗ Redirect to login
```

### 5.2 Authorization Levels

```
Guest (Not Logged In)
  ├─ View public pages
  ├─ View team profiles
  └─ Submit contact forms (no profile access)

Team Member (Logged In)
  ├─ All guest permissions +
  ├─ View own profile
  ├─ Edit own profile
  ├─ View inquiries for own division
  └─ Access member dashboard

Admin (Logged In)
  ├─ All team member permissions +
  ├─ Manage all users
  ├─ Edit any profile
  ├─ View all analytics
  ├─ Manage payments
  ├─ Configure system
  └─ Access admin dashboard
```

### 5.3 Security Layers

```
Request
  ↓
[HTTPS/SSL] ────────────────────► Encrypts data in transit
  ↓
[CSRF Token] ───────────────────► Prevents form hijacking
  ↓
[Rate Limiting] ────────────────► Prevents brute force
  ↓
[Validation] ───────────────────► Checks input format
  ↓
[SQL Injection Prevention] ─────► Parameterized queries
  ↓
[XSS Prevention] ───────────────► HTML escaping
  ↓
[Authentication] ──────────────► Verifies identity
  ↓
[Authorization] ───────────────► Checks permissions
  ↓
[Password Hashing] ────────────► Bcrypt/Argon2
  ↓
Database Storage
```

---

## 6. API ARCHITECTURE

### 6.1 RESTful API Design

**REST Principles:**

```
Resource: /api/team-members
Resource ID: /api/team-members/{id}
Resource Collection: /api/team-members?filter=division_1

Standard HTTP Methods:
GET    /api/team-members           → Retrieve all members
GET    /api/team-members/{id}      → Retrieve one member
POST   /api/team-members           → Create new member
PUT    /api/team-members/{id}      → Update member
DELETE /api/team-members/{id}      → Delete member
```

### 6.2 API Response Format

**Success Response:**
```json
{
  "success": true,
  "status": 200,
  "message": "Team member retrieved successfully",
  "data": {
    "id": 1,
    "name": "Atif Safdar",
    "division": "FBR Taxation",
    "phone": "+92-300-XXXX"
  },
  "timestamp": "2026-02-04T10:30:00Z"
}
```

**Error Response:**
```json
{
  "success": false,
  "status": 404,
  "message": "Team member not found",
  "errors": {
    "id": "Team member with ID 999 does not exist"
  },
  "timestamp": "2026-02-04T10:30:00Z"
}
```

---

## 7. REAL-TIME FEATURES ARCHITECTURE

### 7.1 WebSocket Implementation

**Purpose:** Real-time notifications without page refresh

```
Client (Browser)
     ↕ (WebSocket Connection - persistent)
WebSocket Server (Pusher/Socket.io)
     ↕ (Event Listeners)
Laravel Application
     ↓
Event triggered (payment received, inquiry submitted)
     ↓
Event broadcast to WebSocket server
     ↓
WebSocket server sends to subscribed clients
     ↓
Client receives and displays notification
```

**Use Cases in Our App:**
- Admin receives notification when new inquiry arrives
- Team member receives message when client contacts them
- Payment confirmation appears in real-time
- Form submission updates without page reload

### 7.2 Webhook Implementation

**Purpose:** External systems notify our app of events

```
External Service (Stripe, WhatsApp, etc)
         ↓ (HTTP POST Request)
Our Server Webhook Endpoint
         ↓
Webhook Handler (validates signature)
         ↓
Process Event (update database, send notification)
         ↓
Return 200 OK to external service
```

**Example: Payment Webhook**

```
1. User pays $100 on Stripe
2. Stripe webhook: POST /webhooks/stripe
3. Verify webhook signature (ensures it's from Stripe)
4. Payment status: SUCCESS
5. Create Payment record in database
6. Send confirmation email
7. Broadcast WebSocket event (real-time notification)
8. Return 200 OK to Stripe
```

---

## 8. CACHING STRATEGY

### 8.1 Caching Layers

```
Request
  ↓
[Browser Cache] ───────► Static assets (CSS, JS, images)
  ↓ (if not cached)
[CDN Cache] ────────────► Distributed static content
  ↓ (if not cached)
[Application Cache] ────► Database query results, rendered pages
  ↓ (if not cached)
[Database Query]
  ↓
Response
```

### 8.2 What to Cache

**Cache Heavy Operations:**
```php
// Team members list (rarely changes)
Cache::remember('team-members', 3600, function () {
    return TeamMember::with('division')->get();
});

// Testimonials (rarely changes)
Cache::remember('testimonials', 3600, function () {
    return Testimonial::latest()->get();
});

// Division data
Cache::remember('divisions', 86400, function () {
    return Division::all();
});
```

**Invalidate Cache When:**
```
Team member updates profile → Clear team-members cache
New testimonial added → Clear testimonials cache
Division updated → Clear divisions cache
Payment processed → Clear analytics cache
```

---

## 9. FILE STORAGE & MEDIA MANAGEMENT

### 9.1 Image Storage Architecture

```
User uploads image via form
        ↓
Laravel validates file (type, size)
        ↓
Store temporarily in /storage/uploads
        ↓
Generate thumbnail
        ↓
Upload to Google Drive (async job)
        ↓
Store Google Drive ID in database
        ↓
Delete from server storage
        ↓
Display image using Google Drive URL in views
```

**Why Google Drive?**
- Offsite backup
- No server storage limits
- Easy access and sharing
- Automatic scaling

---

## 10. SECURITY ARCHITECTURE

### 10.1 Security Layers (Defense in Depth)

```
Layer 1: Infrastructure
├─ HTTPS/SSL encryption
├─ Firewall rules
└─ DDoS protection

Layer 2: Application
├─ Input validation
├─ CSRF token protection
├─ XSS prevention
├─ SQL injection prevention
└─ Rate limiting

Layer 3: Authentication
├─ Secure password hashing (Bcrypt/Argon2)
├─ Session management
├─ Login attempt logging
└─ Account lockout after failed attempts

Layer 4: Authorization
├─ Role-based access control
├─ Permission middleware
├─ Policy checks
└─ Resource ownership verification

Layer 5: Data
├─ Encrypted sensitive data
├─ Regular backups
├─ Data validation
└─ Audit logging
```

### 10.2 Password Security

```
User enters password
        ↓
[Length check] ──NO→ "Password too short"
        ↓ YES
[Complexity check] ──NO→ "Password needs symbols/numbers"
        ↓ YES
[Hash password] with Bcrypt (one-way encryption)
        ↓
[Store in database]
        ↓
Never store plaintext passwords!
        ↓
Login: Compare hashed input with stored hash
```

---

## 11. DEPLOYMENT ARCHITECTURE

### 11.1 Local Development

```
XAMPP (Windows Machine)
├─ Apache (Port 80 / 8000)
├─ MySQL (Port 3306)
├─ PHP 8.2+
└─ Laravel Dev Server
```

### 11.2 Production Deployment

```
Web Server (Apache/Nginx)
     ↓
Laravel Application (Optimized)
     ↓
MySQL Database
     ↓
Redis Cache (optional)
     ↓
Google Drive API
     ↓
External Services (Stripe, WhatsApp, etc)
```

---

## 12. DESIGN PRINCIPLES FOLLOWED

### 12.1 SOLID Principles

**S - Single Responsibility**
- Each class has one reason to change
- Controllers handle requests, not business logic
- Services handle business logic

**O - Open/Closed**
- Open for extension, closed for modification
- Use inheritance and interfaces
- Add features without changing existing code

**L - Liskov Substitution**
- Subtypes must be usable in place of parent
- Consistent interfaces across implementations

**I - Interface Segregation**
- Many specific interfaces better than one general
- Clients depend on what they use

**D - Dependency Injection**
- Depend on abstractions, not concrete classes
- Laravel auto-injects dependencies

### 12.2 DRY (Don't Repeat Yourself)

**Reusable Components:**
- Blade components for common UI elements
- Traits for shared methods
- Services for shared business logic
- Middleware for shared request handling

---

## 13. SCALABILITY CONSIDERATIONS

### 13.1 Horizontal Scaling

```
As user base grows:

Single Server
     ↓
[Load Balancer]
├─ Server 1
├─ Server 2
└─ Server N

Shared Database
├─ Primary (write)
└─ Replicas (read)

Shared Cache
├─ Redis Cluster
└─ Distributed sessions
```

### 13.2 Performance Optimization

```
Strategies (in order of importance):
1. Database optimization (proper indexes, query efficiency)
2. Caching (cache frequent queries)
3. Lazy loading (load data only when needed)
4. Pagination (split large result sets)
5. CDN (distribute static assets globally)
6. Horizontal scaling (add more servers)
```

---

## 14. ARCHITECTURE DECISIONS LOG

**Decision 1: Why Laravel?**
- Chosen: Laravel (PHP framework)
- Alternative: Node.js/Express, Python/Django
- Reason: Laravel has best-in-class features, excellent for rapid development, perfect learning curve

**Decision 2: Why MVC?**
- Chosen: MVC pattern
- Alternative: Event-driven, Functional
- Reason: Industry standard, easy to understand, proven in production apps

**Decision 3: Database Normalization**
- Chosen: Third Normal Form (3NF)
- Alternative: Denormalization for performance
- Reason: Correct design foundation, can optimize later with caching

**Decision 4: Theming Approach**
- Chosen: CSS Variables + distinct imagery per division
- Alternative: Separate theme engine, database-driven theming
- Reason: Simple, performant, maintainable

**Decision 5: Image Storage**
- Chosen: Google Drive API integration
- Alternative: Local storage, AWS S3, Cloudinary
- Reason: Free tier available, user can manage files, reduces server load

---

## 15. NEXT PHASE ARCHITECTURE

**Phase 2 (Authentication):**
- Implement Laravel Auth scaffolding
- Create admin and team member middleware
- Build login/register views
- Set up password reset functionality

---

**Architecture Version:** 1.0  
**Last Updated:** February 4, 2026  
**Next Review:** After Phase 2 Completion

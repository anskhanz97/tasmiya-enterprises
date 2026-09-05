# Phase 4: Services & Testimonials Learning Guide

## Overview

Phase 4 builds upon the profile system from Phase 3 by adding:
- **Services Management** - What Tasmiya Enterprises offers
- **Testimonials System** - Client feedback and reviews
- **Service Ratings** - Star ratings from clients
- **Portfolio Integration** - Link services to case studies

This creates a complete service showcase that builds trust and demonstrates expertise.

---

## 1. Conceptual Architecture

### 1.1 Service Model

Each service represents an offering from Tasmiya Enterprises:

```
Service
├── name (e.g., "Tax Consultation")
├── slug (e.g., "tax-consultation")
├── description
├── long_description
├── icon/image
├── base_price
├── currency
├── is_active (boolean)
├── division_id (FK)
├── Relationships
│   ├── division (many-to-one)
│   ├── profiles (many-to-many via service_profile pivot)
│   └── testimonials (one-to-many)
└── Methods
    ├── getAverageRating()
    ├── getTestimonialsCount()
    ├── getSpecialistsCount()
    └── getVisibleTestimonials()
```

**Why this design:**
- Services belong to divisions (each division offers specific services)
- Services link to profiles (certain experts can provide each service)
- Services have testimonials (clients review the service)
- All data is cacheable for performance

### 1.2 Testimonial Model

Client feedback on services and expertise:

```
Testimonial
├── content (review text)
├── rating (1-5 stars)
├── author_name
├── author_position/role
├── author_company
├── author_image
├── Relationships
│   ├── service (many-to-one)
│   ├── profile (nullable many-to-one)
│   └── metadata
├── Status
│   ├── is_approved (boolean)
│   └── is_featured (boolean)
└── Methods
    ├── isVisible()
    ├── getInitials()
    └── getAvatarUrl()
```

**Why this design:**
- Testimonials can be for a service, a profile, or both
- Approval system protects from spam/inappropriate reviews
- Featured testimonials are highlighted
- All review data captured for trust-building

### 1.3 Relationships Diagram

```
┌─────────────┐
│  Division   │
└──────┬──────┘
       │
       │ has many
       ▼
┌─────────────────────┐        ┌──────────────┐
│     Services        │────────│  Profiles    │
└────────┬────────────┘        │  (many-to-   │
         │                     │   many via   │
         │ has many            │   pivot)     │
         ▼                      └──────────────┘
┌─────────────────────┐
│  Testimonials       │
├─────────────────────┤
├─ rating (1-5)      │
├─ is_approved       │
├─ is_featured       │
└─────────────────────┘
```

---

## 2. Key Concepts Explained

### 2.1 Many-to-Many Relationships

**Services & Profiles:**
```php
// In Service model:
public function profiles() {
    return $this->belongsToMany(Profile::class);
}

// In Profile model:
public function services() {
    return $this->belongsToMany(Service::class);
}

// Usage:
$service = Service::find(1);
$service->profiles; // All experts who provide this service

$profile = Profile::find(1);
$profile->services; // All services this person offers
```

**Why:**
- One service can be provided by multiple experts
- One expert can provide multiple services
- Pivot table tracks the many-to-many relationship

### 2.2 Polymorphic Relationships (Optional)

Testimonials can belong to either a Service OR a Profile:

```php
// In Testimonial model:
public function testimonialable() {
    return $this->morphTo();
}

// In Service model:
public function testimonials() {
    return $this->morphMany(Testimonial::class, 'testimonialable');
}

// In Profile model:
public function testimonials() {
    return $this->morphMany(Testimonial::class, 'testimonialable');
}

// Usage:
$service->testimonials()->get(); // Reviews of this service
$profile->testimonials()->get(); // Reviews of this person
```

**Why:**
- Avoids code duplication
- Single testimonials table serves multiple purposes
- Flexible: testimonial can belong to service, profile, or both

### 2.3 Query Performance - Eager Loading

**Without Eager Loading (N+1 problem):**
```php
$services = Service::all(); // 1 query
foreach ($services as $service) {
    echo $service->division->name;      // N queries (1 per service)
    $service->getAverageRating();       // N queries for testimonials
}
// Total: 1 + N + N = 1 + 2N queries (SLOW)
```

**With Eager Loading (Optimized):**
```php
$services = Service::with(['division', 'testimonials'])
    ->get(); // 3 queries total

foreach ($services as $service) {
    echo $service->division->name;      // Already loaded
    $service->getAverageRating();       // Already have testimonials
}
// Total: 3 queries (FAST)
```

### 2.4 Approval Workflow

```
Client submits testimonial
    ↓
Stored in database (is_approved = false)
    ↓
Admin reviews in dashboard
    ↓
Admin approves (is_approved = true)
    ↓
Appears on website
```

**Benefit:** Prevents spam, inappropriate reviews, and maintains quality.

### 2.5 Validation Strategy

**Service Input:**
- Name: Required, max 100 chars
- Description: Required, max 500 chars
- Long description: Optional
- Base price: Required, numeric, min 0
- Division: Required, must exist
- Profiles: Optional array of profile IDs

**Testimonial Input:**
- Content: Required, 20-500 chars
- Rating: Required, integer 1-5
- Author name: Required, max 100 chars
- Author company: Optional, max 100 chars
- Service/Profile: At least one required

---

## 3. Database Design

### 3.1 Services Table

```sql
CREATE TABLE services (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT NOT NULL,              -- Short description
    long_description LONGTEXT,              -- Detailed description
    icon_url VARCHAR(255),
    image_url VARCHAR(255),
    base_price DECIMAL(10, 2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'PKR',
    division_id BIGINT NOT NULL,
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (division_id) REFERENCES divisions(id) ON DELETE CASCADE,
    INDEX idx_division (division_id),
    INDEX idx_active (is_active)
);
```

**Field Explanations:**
- `slug`: URL-friendly version of name (e.g., "tax-consultation")
- `base_price`: Starting price for the service
- `currency`: Support multiple currencies (PKR, USD, EUR)
- `is_active`: Soft filtering without soft deletes
- Indexes on `division_id` and `is_active` for common queries

### 3.2 Service_Profile Pivot Table

```sql
CREATE TABLE service_profile (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    service_id BIGINT NOT NULL,
    profile_id BIGINT NOT NULL,
    created_at TIMESTAMP,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE,
    FOREIGN KEY (profile_id) REFERENCES profiles(id) ON DELETE CASCADE,
    UNIQUE KEY unique_service_profile (service_id, profile_id),
    INDEX idx_profile (profile_id)
);
```

**Explanation:**
- Tracks which experts provide which services
- Unique constraint prevents duplicates
- Cascade deletes keep data consistent

### 3.3 Testimonials Table

```sql
CREATE TABLE testimonials (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    content TEXT NOT NULL,
    rating TINYINT NOT NULL,              -- 1-5 stars
    author_name VARCHAR(100) NOT NULL,
    author_position VARCHAR(100),         -- Job title
    author_company VARCHAR(100),
    author_image_url VARCHAR(255),
    testimonialable_type VARCHAR(255),    -- Polymorphic: Service or Profile
    testimonialable_id BIGINT NOT NULL,   -- Service ID or Profile ID
    is_approved BOOLEAN DEFAULT false,
    is_featured BOOLEAN DEFAULT false,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    INDEX idx_type_id (testimonialable_type, testimonialable_id),
    INDEX idx_approved (is_approved),
    INDEX idx_featured (is_featured)
);
```

**Field Explanations:**
- `rating`: 1-5 star scale
- `author_position`: "CEO", "IT Manager", etc.
- Polymorphic columns allow testimonials for Services OR Profiles
- `is_approved`: Only approved testimonials show on website
- `is_featured`: Highlights top testimonials
- Indexes on approval status for dashboard queries

---

## 4. Implementation Patterns

### 4.1 Average Rating Calculation

**Method 1: Calculate on demand (simple)**
```php
public function getAverageRating() {
    if ($this->testimonials()->count() === 0) {
        return null;
    }
    return round($this->testimonials()
        ->where('is_approved', true)
        ->avg('rating'), 1);
}
```

**Method 2: Cached (better performance)**
```php
public function getAverageRating() {
    return Cache::remember(
        "service_{$this->id}_rating",
        now()->addHours(12),  // Cache for 12 hours
        function () {
            return $this->testimonials()
                ->where('is_approved', true)
                ->avg('rating');
        }
    );
}
```

### 4.2 Testimonials Listing

**Get testimonials for a service:**
```php
// In ServiceController@show
$testimonials = $service->testimonials()
    ->where('is_approved', true)
    ->orderBy('is_featured', 'desc')  // Featured first
    ->orderBy('created_at', 'desc')
    ->paginate(10);
```

**Get average and count:**
```php
$data = $service->testimonials()
    ->where('is_approved', true)
    ->selectRaw('COUNT(*) as count, AVG(rating) as average')
    ->first();

echo $data->count;     // "24 reviews"
echo $data->average;   // "4.8 stars"
```

### 4.3 Service-Profile Assignment

**Add a service to a profile:**
```php
// In dashboard
$profile->services()->attach($serviceId);

// Add multiple services
$profile->services()->attach([
    $serviceId1,
    $serviceId2,
    $serviceId3
]);

// Remove a service
$profile->services()->detach($serviceId);

// Check if profile offers a service
if ($profile->services()->where('service_id', $serviceId)->exists()) {
    // Service offered
}
```

### 4.4 Search & Filter Pattern

**Filter services by division:**
```php
$services = Service::where('division_id', $divisionId)
    ->where('is_active', true)
    ->with('testimonials')
    ->get();
```

**Search by name or description:**
```php
$query = request('search');
$services = Service::where('is_active', true)
    ->where(function($q) use ($query) {
        $q->where('name', 'like', "%$query%")
          ->orWhere('description', 'like', "%$query%");
    })
    ->with('division')
    ->paginate(12);
```

---

## 5. Feature Workflows

### 5.1 Viewing Services

```
User visits /services
    ↓
ServiceController@index loads:
  - All active services
  - Grouped by division
  - With eager-loaded division & testimonials
    ↓
View displays:
  - Service card with image, description, price
  - Star rating and review count
  - "Learn More" button
```

**Key Code:**
```php
public function index() {
    $services = Service::where('is_active', true)
        ->with(['division', 'testimonials' => function($q) {
            $q->where('is_approved', true);
        }])
        ->get()
        ->groupBy('division_id');
    
    return view('services.index', ['services' => $services]);
}
```

### 5.2 Service Detail View

```
User clicks service card → /services/{service}
    ↓
ServiceController@show loads:
  - Service details
  - All associated profiles (experts)
  - Approved testimonials (paginated)
  - Average rating & count
    ↓
View displays:
  - Full description
  - Price information
  - "Book Service" CTA
  - Experts who provide it
  - Client testimonials
  - Rating breakdown (1★, 2★, 3★, 4★, 5★)
```

### 5.3 Submit Testimonial

```
User clicks "Write Review" → modal form
    ↓
Form validates:
  - Rating (1-5)
  - Content (20-500 chars)
  - Author info
    ↓
Submit to TestimonialController@store
    ↓
Create testimonial with is_approved=false
    ↓
Email notification to admin
    ↓
Show "Thank you! Your review awaits approval"
    ↓
Admin approves in dashboard
    ↓
Testimonial appears on service page
```

### 5.4 Admin Management

**Manage Services:**
```
Admin dashboard /admin/services
  - List all services
  - Create new service
  - Edit service details
  - Assign profiles to service
  - Activate/deactivate service
  - View all testimonials for service
```

**Review Testimonials:**
```
Admin dashboard /admin/testimonials
  - List pending testimonials (is_approved=false)
  - View testimonial details
  - Approve or reject
  - Mark as featured
  - Delete inappropriate reviews
```

---

## 6. Code Organization

### 6.1 Model Methods

**Service Model Methods:**
```php
class Service {
    // Relationships
    public function division()
    public function profiles()
    public function testimonials()
    
    // Query Scopes
    public function scopeActive()
    public function scopeByDivision($division_id)
    public function scopeWithRatings()
    
    // Accessors/Helpers
    public function getAverageRating()
    public function getTestimonialCount()
    public function getSpecialistsCount()
    public function getVisibleTestimonials()
    public function getSlugAttribute() // Slug from name
}
```

**Testimonial Model Methods:**
```php
class Testimonial {
    // Relationships
    public function testimonialable()
    
    // Scopes
    public function scopeApproved()
    public function scopeFeatured()
    
    // Helpers
    public function isVisible()
    public function getInitials()
    public function getAvatarUrl()
}
```

### 6.2 Controller Methods

**ServiceController:**
```php
class ServiceController {
    public function index()           // List all services
    public function show($service)    // Service detail + testimonials
    public function create()          // Form to create service
    public function store()           // Save new service
    public function edit($service)    // Edit form
    public function update()          // Save changes
    public function destroy()         // Delete service
}
```

**TestimonialController:**
```php
class TestimonialController {
    public function store()           // Submit testimonial
    public function show()            // View single testimonial
}

// Admin only:
class Admin\TestimonialController {
    public function index()           // List for review
    public function approve()         // Approve testimonial
    public function reject()          // Reject testimonial
    public function feature()         // Mark as featured
}
```

### 6.3 Request Validation

**StoreServiceRequest:**
```php
rules() {
    return [
        'name' => 'required|max:100|unique:services',
        'description' => 'required|max:500',
        'long_description' => 'nullable|string',
        'base_price' => 'required|numeric|min:0',
        'division_id' => 'required|exists:divisions,id',
        'profiles' => 'nullable|array',
        'profiles.*' => 'exists:profiles,id',
    ];
}
```

**StoreTestimonialRequest:**
```php
rules() {
    return [
        'content' => 'required|string|min:20|max:500',
        'rating' => 'required|integer|min:1|max:5',
        'author_name' => 'required|max:100',
        'author_position' => 'nullable|max:100',
        'author_company' => 'nullable|max:100',
        'service_id' => 'nullable|exists:services,id',
        'profile_id' => 'nullable|exists:profiles,id',
    ];
    // Must have at least service_id OR profile_id
}
```

### 6.4 View Structure

```
resources/views/
├── services/
│   ├── index.blade.php        # Service listing page
│   ├── show.blade.php         # Service detail + reviews
│   ├── create.blade.php       # Create service form
│   ├── edit.blade.php         # Edit service form
│   └── partials/
│       ├── card.blade.php     # Service card component
│       └── review-form.blade.php  # Testimonial form
└── admin/
    ├── testimonials/
    │   ├── index.blade.php    # Review queue for approval
    │   └── show.blade.php     # Review detail
    └── services/
        ├── index.blade.php    # Service management
        └── assign-profiles.blade.php # Add experts
```

---

## 7. Performance Considerations

### 7.1 Eager Loading Strategy

```php
// ❌ BAD: Will cause N+1 queries
$services = Service::all();
foreach ($services as $service) {
    echo $service->division->name;           // Extra queries!
    echo $service->testimonials->count();    // Extra queries!
}

// ✅ GOOD: Only 3 queries
$services = Service::with(['division', 'testimonials'])
    ->get();

// ✅ BETTER: Load only approved testimonials
$services = Service::with([
    'division',
    'testimonials' => function($q) {
        $q->where('is_approved', true);
    }
])->get();
```

### 7.2 Caching Strategy

```php
// Cache service ratings for 12 hours
Cache::remember(
    "service_{$id}_rating",
    now()->addHours(12),
    function() use ($id) {
        return Service::find($id)
            ->testimonials()
            ->where('is_approved', true)
            ->avg('rating');
    }
);

// Clear cache when testimonial approved
// (Fire event or use observer)
Cache::forget("service_{$service_id}_rating");
```

### 7.3 Indexing Strategy

```
Key indexes:
- services.division_id        (common filtering)
- services.is_active          (common filtering)
- testimonials.is_approved    (common filtering)
- testimonials.is_featured    (common sorting)
- service_profile (both FKs)  (relationship lookup)
```

---

## 8. Security Considerations

### 8.1 Authorization

```php
// Service management (admin only)
$this->authorize('create', Service::class);      // Can create services
$this->authorize('update', $service);            // Can edit this service
$this->authorize('delete', $service);            // Can delete this service

// Testimonial management (admin only)
$this->authorize('approve', $testimonial);       // Can approve reviews
```

### 8.2 Input Validation

- Trim whitespace on all text inputs
- Validate URLs (icon_url, image_url)
- Validate price is positive number
- Sanitize HTML in long_description
- Rate limit testimonial submissions (1 per day per IP)

### 8.3 Data Protection

- Don't expose unapproved testimonials in API
- Verify service exists before showing testimonials
- Check division permissions for service editing
- Log all testimonial approvals/rejections

---

## 9. Phase 4 Success Criteria

✅ **Database:**
- Services table created with all columns
- Service_profile pivot table created
- Testimonials table created (polymorphic)
- All indexes added
- 8-12 demo services created
- 15-20 demo testimonials created

✅ **Models:**
- Service model with relationships and methods
- Testimonial model with polymorphic relationship
- Scopes for filtering (active, approved, featured)
- Helper methods (ratings, counts, visibility)

✅ **Controllers:**
- ServiceController (index, show, create, store, edit, update, destroy)
- TestimonialController (store for public)
- AdminTestimonialController (index, approve, reject, feature)

✅ **Views:**
- Services listing page (by division)
- Service detail page (with testimonials)
- Service create/edit forms
- Testimonial form (modal)
- Admin testimonial review queue

✅ **Features:**
- Users can view all services
- Users can see testimonials per service
- Users can submit testimonials (with approval)
- Average ratings calculated and displayed
- Responsive design for all views
- Search/filter functionality

✅ **Documentation:**
- This learning guide
- Completion summary with examples
- Code comments throughout
- Testing instructions

---

## 10. What You'll Learn

By implementing Phase 4, you'll master:

1. **Many-to-Many Relationships** - Services assigned to multiple profiles
2. **Polymorphic Relationships** - Testimonials belonging to different models
3. **Approval Workflows** - Managing user submissions
4. **Star Ratings System** - Calculating and displaying averages
5. **Advanced Queries** - Filtering, grouping, and aggregation
6. **Caching** - Performance optimization with cache
7. **Query Optimization** - Eager loading and eager loading constraints
8. **Admin Interfaces** - Managing user-generated content
9. **User Submissions** - Handling and validating user reviews
10. **Responsive Components** - Star ratings, review cards, forms

---

## 11. Integration Points

### 11.1 With Phase 3 (Profiles)

```
Profile has many Services (through pivot)
Service has many Profiles (through pivot)

Profile page:
  - Show services this person offers
  - Show testimonials for this person
  - Link to full service details
```

### 11.2 With Divisions

```
Division has many Services
Service belongs to Division

Service inherits division colors for theming
Service showcases division expertise
```

### 11.3 Future Integration (Phase 5)

```
Services linked to:
- Project portfolio (case studies per service)
- Booking system (schedule service)
- Email notifications (service inquiry)
- Payment processing (service purchase)
```

---

## Next Steps

Ready to build Phase 4? Follow this sequence:
1. Create Service model and migration
2. Create Testimonial model and migration
3. Build ServiceController
4. Build TestimonialController
5. Create views for services and testimonials
6. Add routing
7. Create seeders
8. Test all functionality

Let's make Tasmiya's services shine! 🌟

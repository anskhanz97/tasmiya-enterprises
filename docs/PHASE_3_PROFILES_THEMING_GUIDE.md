# Phase 3: Profiles & Theming - Comprehensive Learning Guide

**Phase Timeline:** Week 4
**Difficulty Level:** Intermediate
**Focus:** User profiles, division-based theming, team showcase

---

## Table of Contents
1. [Conceptual Architecture](#conceptual-architecture)
2. [Key Concepts Explained](#key-concepts-explained)
3. [Database Design](#database-design)
4. [Feature Flowcharts](#feature-flowcharts)
5. [Implementation Strategy](#implementation-strategy)
6. [Common Patterns](#common-patterns)

---

## Conceptual Architecture

### What is Phase 3 About?

In Phase 2, we built the **backend infrastructure** (authentication, authorization, user management). In Phase 3, we're building the **user-facing features** that showcase team members and create personalized, division-specific experiences.

### The Three Pillars of Phase 3

```
┌─────────────────────────────────────────────────────────────┐
│                     PHASE 3: PROFILES                        │
├──────────────────────┬──────────────────┬──────────────────┤
│                      │                  │                  │
│  1. TEAM SHOWCASE    │  2. PROFILE MGMT │  3. THEMING      │
│  ─────────────────   │  ─────────────── │  ─────────────  │
│  • Home page grid    │  • Show page     │  • CSS variables │
│  • Member cards      │  • Edit page     │  • Color schemes │
│  • Quick links       │  • Image upload  │  • Division UI   │
│  • Call-to-action    │  • WhatsApp CTA  │  • Custom fonts  │
│                      │  • Google Drive  │  • Animations    │
└──────────────────────┴──────────────────┴──────────────────┘
```

### User Journey

```
Visitor arrives at homepage
    ↓
Sees attractive team showcase with 4 member cards
    ↓
Clicks on team member card
    ↓
Views their division-themed profile page
    ↓
Sees their expertise, specializations, contact info
    ↓
Clicks WhatsApp button to contact directly
    (Integration placeholder for Phase 5)
```

---

## Key Concepts Explained

### 1. Profile Management (Why Separate from User?)

**Question:** Why do we need a separate profile system? Why not just put everything in the User model?

**Answer:** The separation of concerns principle:
- **User model** = Authentication & authorization (technical)
- **Profile model** = Display & personalization (business logic)

This allows:
- Users without profiles (accounts not yet activated)
- Multiple profiles per user (future feature)
- Independent scaling (profile data separate from auth)
- Better organization as the system grows

```php
// User model: WHO can access?
public function isAdmin(): bool { ... }

// Profile model: WHAT do they show?
public function getSpecializations(): array { ... }
public function getDivisionTheme(): array { ... }
```

### 2. Authorization Policies - The "Who Can Edit?"

In Phase 2, you learned that policies answer: **"Is this person allowed to do this action?"**

For profiles:
- **View:** Anyone can view (public profile)
- **Edit:** Only the profile owner or admins
- **Delete:** Only admins
- **Restore:** Only super admins

```
Policy Decision Tree:

    Can user edit profile?
        ↓
    Is user the owner?
        ↓─YES→ Allow
        ↓─NO
    Is user an admin?
        ↓
        ├─YES→ Allow (admins manage all)
        └─NO→ Deny (403 Forbidden)
```

### 3. Resource-Based Routing

Standard CRUD routes for profiles:

```
GET    /profile/{profile}           → Show public profile
GET    /profile/{profile}/edit      → Edit form (needs authorization)
PUT    /profile/{profile}           → Update profile (needs authorization)
DELETE /profile/{profile}           → Delete profile (admin only)
```

This is the standard RESTful pattern Laravel uses.

### 4. Division-Based Theming - The "What Does It Look Like?"

Each division has a unique visual identity:

**FBR Taxation:**
- Primary: Navy Blue (#1e3a8a)
- Accent: Gold (#f59e0b)
- Theme: Professional, legal, trustworthy

**IT & Digital Services:**
- Primary: Purple (#7c3aed)
- Accent: Pink (#ec4899)
- Theme: Modern, innovative, tech-forward

**Technical Support:**
- Primary: Orange (#f97316)
- Accent: Red (#ef4444)
- Theme: Fast, responsive, action-oriented

**Implementation:** CSS custom properties (CSS variables)

```css
:root {
  --primary-color: #1e3a8a;
  --accent-color: #f59e0b;
  --text-primary: #ffffff;
  --background-gradient: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
}

.profile-header {
  background: var(--background-gradient);
  color: var(--text-primary);
}

.cta-button {
  background: var(--accent-color);
}
```

### 5. Eager Loading - Why It Matters

When you show a profile, you need:
- User data (name, email)
- Division data (colors, name, theme)
- Relationships (services, testimonials, projects - Phase 4+)

**Bad approach (N+1 problem):**
```php
$profiles = Profile::all(); // 1 query
foreach ($profiles as $profile) {
    echo $profile->user->name;        // N queries (1 per profile)
    echo $profile->division->theme;   // N more queries
}
```

**Good approach (eager loading):**
```php
$profiles = Profile::with(['user', 'division'])->get(); // 2 queries
foreach ($profiles as $profile) {
    echo $profile->user->name;        // Already loaded
    echo $profile->division->theme;   // Already loaded
}
```

This is a fundamental Laravel performance optimization.

### 6. Form Submission Lifecycle

When someone edits their profile:

```
User submits form
    ↓
Laravel Form Request validates
    ├─ Authorization check: Is this their profile?
    ├─ Data validation: Valid email? Strong password?
    └─ Data sanitization: Clean up input
    ↓─ If invalid, return errors
    ↓
ProfileController@update receives validated data
    ↓
Update the database
    ↓
Redirect with success message
    ↓
Display updated profile
```

### 7. Image Handling - Preparation for Phase 5

We'll prepare the infrastructure for Google Drive integration:

```php
// Phase 3: Store placeholder URL
$profile->profile_image_url = 'https://placeholder.com/avatar';

// Phase 5: Integration
// $profile->profile_image_url = $googleDrive->upload($image);
```

---

## Database Design

### New Table: profiles

```sql
CREATE TABLE profiles (
    id BIGINT PRIMARY KEY,
    user_id BIGINT UNIQUE,           -- One profile per user
    bio TEXT,                        -- Short biography
    specializations JSON,            -- ["Tax Planning", "Compliance"]
    experience_years INT,            -- Years in the industry
    profile_image_url VARCHAR(255),  -- Avatar (Phase 5: Google Drive)
    banner_image_url VARCHAR(255),   -- Cover photo (Phase 5: Google Drive)
    social_links JSON,              -- {"whatsapp": "03001234567", ...}
    qualifications JSON,            -- ["CPA", "BS Accounting", ...]
    languages JSON,                 -- ["Urdu", "English", "Arabic"]
    consultation_fee DECIMAL(8,2),  -- Hourly rate (for services)
    is_visible BOOLEAN DEFAULT true,-- Can be hidden from showcase
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

**Why JSON columns?**
- Flexibility: Can add/remove specializations without schema changes
- Practical: These don't need to be queried individually
- Laravel native: Eloquent casts JSON to arrays automatically

### Migration Strategy

We'll create a new migration:
```bash
php artisan make:migration create_profiles_table
php artisan make:migration add_profile_image_to_users_table
```

Actually, we'll add image URL to profiles table (not users), keeping concerns separated.

---

## Feature Flowcharts

### Flowchart 1: View Public Profile

```
Visitor clicks "View Profile"
    ↓
Route: GET /profile/{profile}
    ↓
Controller loads profile with eager loading
    $profile = Profile::with(['user', 'division'])->find($id)
    ↓
Authorization check: Can view this profile?
    (Everyone can view, so check if it's visible)
    if (!$profile->is_visible && !auth()->user()?->isAdmin())
        return 403
    ↓
Load division theme colors
    $theme = $profile->division->getThemeColors()
    ↓
Render view with theme
    return view('profiles.show', compact('profile', 'theme'))
    ↓
Browser applies division-specific CSS
    <link rel="stylesheet" href="/css/theme-{division}.css">
    ↓
Display profile with:
    - Name, title, bio
    - Specializations
    - Experience
    - Division-branded colors
    - WhatsApp CTA button
```

### Flowchart 2: Edit Own Profile

```
User clicks "Edit Profile"
    ↓
Authorization check: Is this your profile?
    Can only edit own profile (or admin editing anyone)
    ↓
GET /profile/{profile}/edit
    ↓
Load ProfileEditRequest form
    Display form with current data
    ↓
User submits updated form
    ↓
ProfileUpdateRequest validates:
    - Email must be unique (except own email)
    - Bio under 500 characters
    - Specializations as array
    - Image URL format valid
    ↓
ProfileController@update
    ↓
Update database
    $profile->update($validated)
    ↓
Redirect to profile view
    return redirect()->route('profiles.show', $profile)
    with success message
```

### Flowchart 3: Team Showcase on Homepage

```
Visitor lands on homepage
    ↓
Controller loads homepage
    ↓
Query all visible profiles
    $profiles = Profile::with(['user', 'division'])
                ->where('is_visible', true)
                ->orderBy('created_at', 'desc')
                ->get()
    ↓
Group by division (optional)
    $byDivision = $profiles->groupBy('division_id')
    ↓
Render team showcase grid
    4 cards in 2x2 or responsive layout
    ↓
Each card shows:
    - Profile image
    - Name
    - Division badge
    - Bio preview (first 100 chars)
    - "View Profile" link
    ↓
Clicking card → Route to profile show page
```

---

## Implementation Strategy

### Step 1: Database Foundation
1. Create `profiles` table migration
2. Add relationships in User and Division models
3. Run migration and verify schema

### Step 2: Model Layer
1. Create `Profile` model
2. Add relationships: `belongsTo` User, Division
3. Add accessors/mutators for JSON columns
4. Add scopes: `visible()`, `byDivision()`

### Step 3: Authorization Layer
1. Create `ProfilePolicy` with view, update, delete methods
2. Register policy in `AppServiceProvider`
3. Test authorization

### Step 4: Controller Layer
1. Create `ProfileController` with 5 methods
   - `show($profile)` - View public profile
   - `edit($profile)` - Show edit form
   - `update(ProfileUpdateRequest $request, $profile)` - Save changes
   - `index()` - List all visible profiles (for team showcase)
   - `destroy($profile)` - Delete profile (admin only)

### Step 5: Request Layer
1. Create `ProfileUpdateRequest` with validation rules
   - Unique email check (excluding own)
   - Text length limits
   - JSON array validation
   - Image URL format

### Step 6: View Layer
1. Create `profiles/show.blade.php` - Public profile display
2. Create `profiles/edit.blade.php` - Edit form
3. Modify `home.blade.php` - Team showcase grid
4. Update `layouts/app.blade.php` - Add theme CSS link

### Step 7: Styling Layer
1. Create `resources/css/themes.css` - CSS variables for colors
2. Create `resources/css/profiles.css` - Profile-specific styles
3. Create `resources/css/showcase.css` - Team showcase grid

### Step 8: Routing
1. Add resource routes: `Route::resource('profiles', ProfileController::class)`
2. Add visibility constraints
3. Document in comments

### Step 9: Seeders
1. Update `DivisionSeeder` if needed
2. Create `ProfileSeeder` with realistic profile data for each user

### Step 10: Documentation
1. Create implementation guide with code walkthroughs
2. Create testing instructions
3. Document the theming system

---

## Common Patterns

### Pattern 1: Conditional Authorization

```php
// In ProfilePolicy
public function update(User $user, Profile $profile): bool
{
    // User can edit their own profile
    if ($user->id === $profile->user_id) {
        return true;
    }
    
    // Admin can edit any profile
    if ($user->isAdmin()) {
        return true;
    }
    
    // Otherwise denied
    return false;
}
```

### Pattern 2: Resource Route Authorization

```php
// In ProfileController
public function edit(Profile $profile)
{
    // This uses the policy automatically
    $this->authorize('update', $profile);
    
    return view('profiles.edit', compact('profile'));
}
```

### Pattern 3: JSON Column Handling

```php
// Cast JSON to array/object
protected $casts = [
    'specializations' => 'array',
    'social_links' => 'array',
    'qualifications' => 'array',
];

// Use as array
$profile->specializations[] = 'New Skill';
$profile->save(); // Automatically encoded back to JSON

// Access in view
@foreach($profile->specializations as $skill)
    <span>{{ $skill }}</span>
@endforeach
```

### Pattern 4: Theme CSS Generation

```php
// In ProfileController@show
$theme = $profile->division->getThemeColors();

return view('profiles.show', [
    'profile' => $profile,
    'theme' => $theme,
    'themeClass' => 'theme-' . $profile->division->slug,
]);
```

Then in view:
```blade
<style>
    :root {
        --primary-color: {{ $theme['primary'] }};
        --accent-color: {{ $theme['accent'] }};
        --text-color: {{ $theme['text'] }};
    }
</style>
```

### Pattern 5: Eager Loading in Controllers

```php
// Always eager load relationships to prevent N+1
$profiles = Profile::with(['user', 'division'])
    ->where('is_visible', true)
    ->get();

// Now these are free (already loaded)
foreach ($profiles as $profile) {
    echo $profile->user->name;      // No extra query
    echo $profile->division->theme; // No extra query
}
```

---

## Why This Approach?

### Separation of Concerns
- **User model**: Handles login, permissions
- **Profile model**: Handles display, specializations
- **Division model**: Handles branding, theming
- Each has a single responsibility

### Scalability
- Can add millions of profiles without affecting auth system
- Profile features don't slow down login process
- Can move profiles to separate database later

### Maintainability
- Clear folder structure: `Controllers/ProfileController.php`
- Clear naming: `ProfilePolicy`, `ProfileUpdateRequest`
- Clear relationships: `Profile::with('user')`

### Security
- Authorization checks at multiple layers
- Form request validation before controller
- Policy authorization in controller
- Middleware could add additional checks

---

## Next Steps (This Phase)

1. Create Profile model with migrations
2. Build ProfileController with CRUD operations
3. Create authorization policies
4. Build views with team showcase
5. Implement division-based theming system
6. Create comprehensive documentation

**Estimated Time:** 4-6 hours for complete implementation
**Complexity:** Intermediate (combines concepts from Phase 2)
**Learning Value:** High (brings together multiple Laravel features)

---

## Phase 3 Success Criteria

✅ Profile model fully functional with relationships
✅ Profile CRUD operations working
✅ Authorization policies protecting updates/deletes
✅ Team showcase displaying all visible profiles
✅ Division-specific theming applied
✅ Profile images uploading (placeholder for Phase 5)
✅ All views responsive and professional
✅ Code well-documented with inline comments
✅ Database properly seeded with realistic data
✅ Comprehensive documentation provided

Let's build this! 🚀

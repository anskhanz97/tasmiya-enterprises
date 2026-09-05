# 🔐 PHASE 2: AUTHENTICATION & AUTHORIZATION SYSTEM
**Complete Implementation Guide with Educational Materials**

---

## 📋 OVERVIEW

### What You'll Build
A secure, role-based authentication system where:
- Team members (4 total) can log in with their email/password
- Admin can manage all user accounts
- Users can only edit their own profiles (unless admin)
- Login/signup forms are hidden from the public (no links in UI)
- Sessions expire after 30 minutes of inactivity
- Password reset functionality available

### Why This Approach?
- **Role-based**: Scales to any number of users/roles
- **Middleware-protected**: Routes automatically checked for auth
- **Policies**: Fine-grained control (user can edit only own profile)
- **Hidden URLs**: Security through obscurity + explicit checks
- **Session-based**: Simpler than API tokens for this use case

### Learning Outcomes
By the end, you'll understand:
1. How Laravel's authentication works under the hood
2. What middleware does and why it's useful
3. How authorization policies work
4. The request validation lifecycle
5. Blade template syntax for auth checks
6. Password hashing and security best practices

---

## 🏗️ ARCHITECTURE OVERVIEW

### Authentication Flow Diagram

```
Guest User
    ↓
Visit /login (hidden URL, only accessible if you know it)
    ↓
Submit email + password
    ↓
[AuthController@store validates]
    ↓
Check email in database
    ↓
Hash password & compare ← (never store plain passwords!)
    ↓
✅ Match? → Create session, set auth cookies → Redirect to dashboard
❌ No match? → Show error message
    ↓
[Middleware@handle] Checks for session cookie on every request
    ↓
Is authenticated? → Continue to requested page
Not authenticated? → Redirect to /login
    ↓
[Policy@view] Checks: Can this user edit this profile?
    ↓
User owns profile OR is admin? → Allow edit
    ↓
Not owner & not admin? → Forbidden 403 error
```

### Files We'll Create

```
app/
├── Http/
│   ├── Controllers/
│   │   └── AuthController.php          ← Login/Register/Logout
│   ├── Requests/
│   │   ├── LoginRequest.php            ← Validate login form
│   │   ├── RegisterRequest.php         ← Validate registration
│   │   └── ProfileUpdateRequest.php    ← Validate profile edits
│   └── Middleware/
│       ├── Authenticate.php            ← Check if logged in
│       ├── Administrator.php           ← Check if admin
│       └── RedirectIfAuthenticated.php ← Redirect logged-in users from login page
│
├── Policies/
│   └── ProfilePolicy.php               ← Check profile ownership
│
├── Models/
│   └── User.php                        ← Updated with divisions
│
database/
├── migrations/
│   ├── 2026_02_04_000003_create_divisions_table.php
│   ├── 2026_02_04_000004_create_profiles_table.php
│   └── 2026_02_04_000005_add_role_and_division_to_users_table.php
│
resources/
├── views/
│   ├── auth/
│   │   ├── login.blade.php             ← Login form
│   │   ├── register.blade.php          ← Registration form
│   │   └── reset-password.blade.php    ← Password reset
│   └── layouts/
│       ├── auth-layout.blade.php       ← Auth pages layout
│       └── app-layout.blade.php        ← Dashboard layout
│
├── css/
│   └── auth.css                        ← Auth page styling
│
routes/
└── web.php                             ← Updated with auth routes
```

---

## 🗄️ DATABASE DESIGN

### Divisions Table (Parent)
```sql
CREATE TABLE divisions (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,           -- "FBR Taxation", "IT & Digital", "Technical Support"
    slug VARCHAR(255) UNIQUE NOT NULL,    -- "fbr-taxation" (for URLs)
    tagline VARCHAR(255),                 -- "Let's Be Money Smart"
    description TEXT,
    theme_color VARCHAR(7),               -- "#1e3a8a" (hex color)
    primary_color VARCHAR(7),
    secondary_color VARCHAR(7),
    icon_path VARCHAR(255),               -- path to division icon
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Seed data:
INSERT INTO divisions VALUES
(1, 'FBR Taxation', 'fbr-taxation', 'Let''s Be Money Smart', '...', '#1e3a8a', '#3b82f6', '#d97706', '/icons/tax.svg', ...),
(2, 'IT & Digital', 'it-digital', 'If you can think it, we can build it', '...', '#7c3aed', '#06b6d4', '#000000', '/icons/code.svg', ...),
(3, 'Technical Support', 'tech-support', 'Let Technology do the Work for you', '...', '#f97316', '#334155', '#eab308', '/icons/tools.svg', ...);
```

### Users Table (Modified)
```sql
-- Add columns to existing users table
ALTER TABLE users ADD COLUMN (
    role ENUM('guest', 'team_member', 'admin', 'super_admin') DEFAULT 'guest',
    division_id BIGINT UNSIGNED,
    is_active BOOLEAN DEFAULT FALSE,
    phone VARCHAR(20),
    whatsapp_number VARCHAR(20),
    bio TEXT,
    profile_image_url VARCHAR(255),
    last_login_at TIMESTAMP NULL,
    email_verified_at TIMESTAMP NULL,
    FOREIGN KEY (division_id) REFERENCES divisions(id)
);

-- Indexes for performance
INDEX idx_role (role),
INDEX idx_division (division_id),
INDEX idx_email (email)
```

### Why These Columns?
- `role`: Used in middleware/gates to check permissions
- `division_id`: Link to which division user belongs
- `is_active`: Admin can deactivate accounts without deleting
- `last_login_at`: Track activity, identify inactive accounts
- `email_verified_at`: Laravel's built-in email verification field
- `profile_image_url`: Store Google Drive image link
- Indexed columns: Speed up database queries (especially email lookups)

---

## 🔑 KEY CONCEPTS EXPLAINED

### 1️⃣ Password Hashing (Why we don't store plain passwords)

**Wrong Way (NEVER DO THIS):**
```php
// ❌ WRONG - Don't do this!
$user->password = request('password');  // Stores plain text "password123"
$user->save();
```

**Why it's bad:**
- If database is breached, hackers get everyone's password
- Passwords can be read by your own employees
- Illegal under GDPR/data protection laws

**Right Way:**
```php
// ✅ CORRECT
$user->password = Hash::make(request('password'));  // Hashes to "$2y$12$abcdef..."
$user->save();

// Later, to verify:
if (Hash::check('password123', $user->password)) {
    // Correct password entered
}
```

**How it works:**
```
"password123" → bcrypt algorithm → "$2y$12$iNVj7K4Xj8K4Xj8K4Xj8K4Xj8K4Xj8K4Xj8K4Xj8K4Xj8K4X"
                                   ↑
                    Hash changes every time, but matches correctly
                    Can't reverse-hash to get original password
```

### 2️⃣ Middleware (Gatekeepers)

Middleware is code that runs BEFORE your route handler. Think of it like security checkpoints:

```
Request comes in
    ↓
[Middleware 1: Check if JSON] → Is it JSON? → Continue : Return error
    ↓
[Middleware 2: Verify token] → Valid token? → Continue : Return 401
    ↓
[Middleware 3: Check role] → Is admin? → Continue : Return 403
    ↓
Route handler executes
    ↓
Response sent back
```

**Built-in Laravel middleware:**
- `auth`: User must be logged in
- `guest`: User must NOT be logged in
- `admin`: User must have admin role (custom)

**Usage in routes:**
```php
Route::post('/login', [AuthController::class, 'store'])->middleware('guest');
    // ↑ Only guests can access (logged-in users get redirected)

Route::post('/profile/{id}', [ProfileController::class, 'update'])
    ->middleware('auth');  // Only logged-in users
```

### 3️⃣ Authorization Policies

Middleware checks **"Can this user access this route?"**  
Policies check **"Can this user do this specific action?"**

**Example:**
```php
// Middleware: Is user logged in? ✅ Yes
// Policy: Is user trying to edit their own profile? ❌ No (editing someone else's)
// Result: 403 Forbidden error
```

**Policy code:**
```php
class ProfilePolicy {
    public function update(User $user, Profile $profile): bool {
        // Owner can edit their own profile
        if ($user->id === $profile->user_id) {
            return true;
        }
        
        // Admin can edit any profile
        if ($user->isAdmin()) {
            return true;
        }
        
        // Otherwise, not allowed
        return false;
    }
}
```

### 4️⃣ Form Requests (Automatic Validation)

Form Requests validate input AND authorize the action in one place:

```php
class ProfileUpdateRequest extends FormRequest {
    public function authorize(): bool {
        // Check if user is allowed to update this profile
        return $this->user()->can('update', $this->profile);
    }
    
    public function rules(): array {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $this->user()->id,
            'phone' => 'nullable|digits:11',
        ];
    }
    
    public function messages(): array {
        return [
            'email.unique' => 'This email is already taken.',
            'phone.digits' => 'Phone must be 11 digits.',
        ];
    }
}
```

**Benefits:**
- Validation logic separated from controller
- Authorization checked before controller
- Cleaner, more testable code
- Reusable across multiple controllers

---

## 🛠️ IMPLEMENTATION STEPS

### Step 1: Create Migrations

#### Migration 1: Divisions Table
```php
// database/migrations/2026_02_04_000003_create_divisions_table.php

public function up(): void {
    Schema::create('divisions', function (Blueprint $table) {
        $table->id();
        $table->string('name')->unique();        // "FBR Taxation"
        $table->string('slug')->unique();        // "fbr-taxation"
        $table->string('tagline')->nullable();   // "Let's Be Money Smart"
        $table->text('description')->nullable();
        $table->string('theme_color')->default('#000000');      // Primary color hex
        $table->string('primary_color')->default('#000000');
        $table->string('secondary_color')->default('#ffffff');
        $table->string('icon_path')->nullable(); // /icons/division.svg
        $table->timestamps();
    });
}

public function down(): void {
    Schema::dropIfExists('divisions');
}
```

#### Migration 2: Add Role/Division to Users
```php
// database/migrations/2026_02_04_000004_add_role_and_division_to_users_table.php

public function up(): void {
    Schema::table('users', function (Blueprint $table) {
        $table->enum('role', ['guest', 'team_member', 'admin', 'super_admin'])
            ->default('guest')
            ->after('password');
        
        $table->foreignId('division_id')
            ->nullable()
            ->after('role')
            ->constrained('divisions')
            ->onDelete('set null');
        
        $table->boolean('is_active')
            ->default(false)
            ->after('division_id');
        
        $table->string('phone')->nullable()->after('is_active');
        $table->string('whatsapp_number')->nullable()->after('phone');
        $table->text('bio')->nullable()->after('whatsapp_number');
        $table->string('profile_image_url')->nullable()->after('bio');
        $table->timestamp('last_login_at')->nullable()->after('profile_image_url');
        
        // Add indexes for performance
        $table->index('role');
        $table->index('division_id');
        $table->index('email');
    });
}

public function down(): void {
    Schema::table('users', function (Blueprint $table) {
        $table->dropForeignKeyConstraints();
        $table->dropColumn([
            'role', 'division_id', 'is_active', 'phone', 
            'whatsapp_number', 'bio', 'profile_image_url', 'last_login_at'
        ]);
        $table->dropIndex(['role', 'division_id', 'email']);
    });
}
```

### Step 2: Update User Model

```php
// app/Models/User.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable {
    use HasFactory, Notifiable;

    // Guarded = all fields except these can be mass-assigned
    // This prevents security issues with form input
    protected $guarded = [];

    protected $hidden = [
        'password',      // Never return password in JSON responses
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',  // ← Automatically hashes on save
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
    ];

    // ========== RELATIONSHIPS ==========
    
    public function division() {
        return $this->belongsTo(Division::class);
    }

    // ========== ACCESSORS (computed properties) ==========

    public function isAdmin(): bool {
        return in_array($this->role, ['admin', 'super_admin']);
    }

    public function isTeamMember(): bool {
        return $this->role === 'team_member';
    }

    public function canEditProfile($profile): bool {
        return $this->id === $profile->user_id || $this->isAdmin();
    }

    // ========== SCOPES (reusable queries) ==========

    public function scopeActive($query) {
        return $query->where('is_active', true);
    }

    public function scopeByRole($query, $role) {
        return $query->where('role', $role);
    }

    public function scopeByDivision($query, $divisionId) {
        return $query->where('division_id', $divisionId);
    }
}
```

**Why the `$casts` array?**
- Automatically converts columns to specific types
- `hashed` = automatically runs `Hash::make()` when saving password
- `datetime` = converts to Carbon dates (allows `->diffForHumans()` etc)
- `boolean` = converts 0/1 to true/false

---

## 🎯 WHY EACH FILE EXISTS

| File | Purpose | When Used |
|------|---------|-----------|
| **AuthController** | Handles login/register/logout | User submits form |
| **LoginRequest** | Validates login form | Before AuthController runs |
| **Authenticate Middleware** | Checks if logged in | Before protected routes |
| **Administrator Middleware** | Checks if admin | Before admin routes |
| **ProfilePolicy** | Checks profile ownership | Before allowing edit |
| **Blade Templates** | HTML forms for login | Browser rendering |
| **User Model** | Database interaction | Everywhere |
| **Division Model** | Database interaction | Division-related queries |

---

## 📝 IMPORTANT CONCEPTS

### Sessions vs Tokens

**Sessions (What we're using):**
```
Browser sends → Server creates session → Server stores in database
                                ↓
                       Browser gets session cookie
                                ↓
                   Browser sends cookie on every request
                                ↓
                   Server verifies cookie, knows who you are
```

**Tokens (For APIs):**
```
Browser sends credentials → Server returns token (JWT)
                                ↓
                Browser stores token in localStorage
                                ↓
           Browser sends token in Authorization header
                                ↓
          Server verifies token, knows who you are
```

**We use sessions because:**
- ✅ Simpler for traditional web apps
- ✅ More secure (httpOnly cookies)
- ✅ Session data lives on server (can't be faked)
- ✅ Natural fit with Laravel

---

## 🔒 SECURITY CHECKLIST

- [ ] Passwords hashed with bcrypt (Laravel default)
- [ ] CSRF token on every form
- [ ] Hidden login/register URLs (add to docs, not in UI)
- [ ] Login attempt rate limiting (prevent brute force)
- [ ] Email verification before account active
- [ ] Admin-only routes protected with middleware
- [ ] Profile edits allowed only by owner or admin
- [ ] Session expires after 30 minutes
- [ ] Password reset via email token (not guessable)
- [ ] Database queries use parameterized queries (Eloquent does this)

---

## 📊 FLOW DIAGRAMS

### Login Process Flow

```
User visits /login
        ↓
[Middleware: guest] Logged in already? → Redirect to dashboard
        ↓ No, continue
    Display login form
        ↓
    User enters email + password
        ↓
POST /login [Middleware: guest]
        ↓
    [LoginRequest validates]
        ↓
    Find user by email
        ↓
    Hash::check(inputPassword, storedHash)?
        ↓ Yes
    Update last_login_at
        ↓
    auth()->login($user) ← Creates session
        ↓
    Redirect to /dashboard
```

### Admin-Only Route Flow

```
User visits /admin/users
        ↓
[Middleware: auth] Logged in? → Redirect to login
        ↓ Yes
[Middleware: administrator] Is admin? → Return 403 Forbidden
        ↓ Yes
    Display admin dashboard
```

### Profile Edit Flow

```
User visits /profile/{id}/edit
        ↓
[Middleware: auth] Logged in? → Redirect to login
        ↓ Yes
[ProfilePolicy] Can edit? → Check: user_id == profile_user_id OR isAdmin()
        ↓ Yes
    Display edit form
        ↓
POST /profile/{id} [ProfileUpdateRequest]
        ↓
[FormRequest] Validates + Authorizes
        ↓
    Update profile
        ↓
    Redirect with success message
```

---

## 🚀 NEXT PHASE ROADMAP

After completing Phase 2, you'll have:
✅ Secure login/register system
✅ Role-based access control
✅ Session management
✅ Protected routes and resources

Phase 3 will add:
- Team member profiles
- Division-specific theming
- Profile editing interface
- Google Drive image integration

---

## 💡 LEARNING TIPS

1. **Understand middleware first** - It's the foundation of auth in Laravel
2. **Use `dd()` to debug** - `dd($user)` shows what's in the variable
3. **Check `auth()` helper** - `auth()->user()` gives current logged-in user
4. **Read error messages carefully** - They usually tell you what's wrong
5. **Use Laravel Tinker** - `php artisan tinker` to test code interactively
6. **Check storage/logs** - Errors are logged when debugging is off

---

This guide covers the "why" and "how" at a conceptual level. As we build each piece, code comments will explain the "what" in detail.

Let's build Phase 2! 🚀

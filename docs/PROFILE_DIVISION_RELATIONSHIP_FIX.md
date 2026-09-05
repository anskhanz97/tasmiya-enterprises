# Profile Division Relationship Fix

**Issue:** `RelationNotFoundException` - Call to undefined relationship [division] on model [App\Models\Profile]

**Root Cause:** The previous fix converted the `division()` relationship method to an attribute accessor (`getDivisionAttribute()`), which works for simple property access but breaks Laravel's eager loading mechanism.

When views try to use `Profile::with('division')`, Laravel looks for a relationship method named `division()`, not an attribute accessor. The attribute accessor approach only works with direct property access like `$profile->division`.

## Solution: HasOneThrough Relationship

Instead of an attribute accessor, we implemented a proper `HasOneThrough` relationship that bridges:
- Profile → User (via user_id FK)
- User → Division (via division_id FK)

### Implementation

**File:** [app/Models/Profile.php](../app/Models/Profile.php)

**Changes:**
1. Added import: `use Illuminate\Database\Eloquent\Relations\HasOneThrough;`

2. Created relationship method:
```php
public function division(): HasOneThrough
{
    return $this->hasOneThrough(
        Division::class,        // Final target model
        User::class,           // Intermediate model
        'id',                  // User table foreign key (profiles.user_id → users.id)
        'id',                  // Division table foreign key (users.division_id → divisions.id)
        'user_id',            // Local key on profiles table
        'division_id'         // Foreign key on users table
    );
}
```

### Why HasOneThrough?

- ✅ Supports eager loading: `Profile::with('division')->get()`
- ✅ Allows relationship queries: `$profile->division()->where(...)`
- ✅ Proper type hints for IDE support
- ✅ Laravel's native relationship pattern
- ✅ Optimal query generation with single JOIN

### Usage

```php
// Direct property access
$division = $profile->division;

// Eager loading (what caused the original error)
$profiles = Profile::with('division')->get();

// In queries
$profiles = Profile::with(['user', 'division'])->where('is_visible', true)->get();
```

## Testing

✅ `Profile::with('division')->first()` - Works
✅ `Profile::with(['user', 'division'])->get()` - Works
✅ Homepage `/` - Loads without errors
✅ Profile collections eager load correctly

## Related Files

- [resources/views/home.blade.php](../resources/views/home.blade.php#L236) - Uses `with(['user', 'division'])`
- [app/Models/User.php](../app/Models/User.php) - Defines User → Division relationship
- [app/Models/Division.php](../app/Models/Division.php) - Target Division model

## Status

✅ **FIXED** - All relationship errors resolved
✅ **TESTED** - Homepage loads without errors
✅ **VERIFIED** - Eager loading works correctly

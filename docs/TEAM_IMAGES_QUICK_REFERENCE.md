# Quick Reference Guide - Team Images & Profile Pages

## 📍 Where to Find Everything

### Home Page (`http://localhost:8000`)

**Team Section:**
- Located: Scroll down to "Our Expert Team" section
- Shows: 8 team member cards (responsive grid)
- Each Card Contains:
  - Team member image (from `public/images/profiles/{firstname}/image.png`)
  - Name
  - Specialization/Title
  - Hover effect: Image zooms, card lifts up

**Image Path Used:**
```
Atif Safdar      → /images/profiles/atif/image.png
Waseem Asghar    → /images/profiles/waseem/image.png
Ans Khan         → /images/profiles/ans/image.png
Nazim Rauf       → /images/profiles/nazim/image.png
```

---

### Profile Pages

**Direct URLs:**
- Atif:   `http://localhost:8000/profiles/1`
- Waseem: `http://localhost:8000/profiles/2`
- Ans:    `http://localhost:8000/profiles/3`
- Nazim:  `http://localhost:8000/profiles/4`

**What You See:**

**1. Top Section (Cover + Profile Photo)**
```
┌─────────────────────────────────────┐
│     Banner Image (400px height)      │
│  (from public/images/profiles/     │
│        {name}/banner.png)          │
│                                     │
│        ┌──────────────┐             │
│        │ Profile Photo│             │
│        │  (220x220px  │             │
│        │  circular)   │             │
│        └──────────────┘             │
│                                     │
│        Name & Title                 │
│        Division Badge               │
│     Stats Row (Experience, etc)     │
│                                     │
│  [Edit] [WhatsApp Contact]         │
└─────────────────────────────────────┘
```

**Image Paths Used:**
```
Profile Image:  /images/profiles/{firstname}/image.png
Banner Image:   /images/profiles/{firstname}/banner.png
```

**2. Main Content (Below Cover)**
```
Left Column (2/3 width):          Right Sidebar (1/3 width):
├─ About                          ├─ Consultation Fee
├─ Specializations               ├─ Contact Information
├─ Professional Experience        ├─ Team Members
├─ Qualifications & Certs        └─ (Division team links)
└─ Languages

Below Both:
└─ Testimonials (if any)
```

---

## 🎯 Image File Organization

```
public/images/profiles/
│
├── atif/
│   ├── image.png           (Home page - team section display)
│   └── banner.png          (Profile page - cover image)
│
├── waseem/
│   ├── image.png
│   └── banner.png
│
├── nazim/
│   ├── image.png
│   └── banner.png
│
└── ans/
    ├── image.png
    └── banner.png
```

**Naming Convention:**
- `image.png` = Profile photo for home page (220x220px, displays as circle)
- `banner.png` = Cover image for profile page (1200x400px wide)

---

## 🔄 How Images Are Loaded

**Step 1: Home Page Team Section**
```
User visits: http://localhost:8000
    ↓
Profile Model: getImageUrl()
    ↓
Check: public/images/profiles/atif/image.png
    ↓
Found? → Display it
Not found? → Check stored profile_image_url
Still not found? → Show placeholder with initials
```

**Step 2: Profile Page**
```
User visits: http://localhost:8000/profiles/1 (Atif's profile)
    ↓
Profile Model: getImageUrl()
    ↓
Load main image: /images/profiles/atif/image.png
    ↓
Profile Model: getBannerImageUrl()
    ↓
Load banner: /images/profiles/atif/banner.png
    ↓
Display both in LinkedIn-style layout
```

---

## 💡 Key Functions Used

### In Profile Model (`app/Models/Profile.php`)

**`getImageUrl(): string`**
```php
// Returns the profile image URL
// Used on: Home page team section
// Filename: image.png

$url = $profile->getImageUrl();
// Output: /images/profiles/atif/image.png
```

**`getBannerImageUrl(): ?string`**
```php
// Returns the banner image URL
// Used on: Profile show page
// Filename: banner.png

$url = $profile->getBannerImageUrl();
// Output: /images/profiles/atif/banner.png
// Or: null (if not found)
```

---

## 📋 Blade Template Usage

### Home Page - Team Card
```blade
@foreach($profiles as $profile)
    <a href="{{ route('profiles.show', $profile) }}" class="team-card">
        <div class="team-image-wrapper">
            <img src="{{ $profile->getImageUrl() }}" class="team-image">
        </div>
        <div class="team-info">
            <div class="team-name">{{ $profile->user->name }}</div>
            <div class="team-title">{{ $profile->specializations[0] }}</div>
        </div>
    </a>
@endforeach
```

### Profile Page - Cover Section
```blade
<div class="profile-banner-container">
    @if($profile->getBannerImageUrl())
        <img src="{{ $profile->getBannerImageUrl() }}" class="profile-banner-img">
    @endif
</div>

<div class="profile-image-wrapper">
    <img src="{{ $profile->getImageUrl() }}" class="profile-image">
</div>
```

---

## 🎨 Styling and Sizing

**Home Page - Team Image:**
- Width: Responsive (grid item width)
- Height: 280px
- Border: 0 (square display)
- Zoom on hover: scale(1.08)
- Object-fit: cover (crops to fit)

**Profile Page - Banner Image:**
- Width: 100% (full width)
- Height: 400px
- Border: 0
- Overlay: Purple gradient on top
- Object-fit: cover

**Profile Page - Profile Image:**
- Width: 220px
- Height: 220px
- Border: 8px white
- Border-radius: 50% (makes it circular)
- Position: Overlays banner (negative margin)
- Shadow: `0 10px 40px rgba(102, 126, 234, 0.3)`
- Zoom on hover: scale(1.05)

---

## ✅ Verification Checklist

Use this to verify everything is working:

**Home Page:**
- [ ] Navigate to `http://localhost:8000`
- [ ] Scroll to "Our Expert Team" section
- [ ] See 4 team member cards with images
- [ ] Images are clear and properly sized
- [ ] Hover effect works (image zooms)
- [ ] Click on a card → Navigate to profile page

**Profile Page (Example: Atif at /profiles/1):**
- [ ] Banner image displays at top (400px height)
- [ ] Profile photo is circular (220x220px)
- [ ] Profile photo overlays banner
- [ ] Name and title display correctly
- [ ] Stats show (experience years, qualifications, etc)
- [ ] About section displays bio
- [ ] Specializations show as tags
- [ ] Contact information visible
- [ ] WhatsApp button works
- [ ] Mobile view is responsive (stacks vertically)

---

## 🔍 Image Reference

**Atif Safdar (ID: 1)**
- Home Image: `public/images/profiles/atif/image.png`
- Profile Banner: `public/images/profiles/atif/banner.png`
- Profile URL: `/profiles/1`

**Waseem Asghar (ID: 2)**
- Home Image: `public/images/profiles/waseem/image.png`
- Profile Banner: `public/images/profiles/waseem/banner.png`
- Profile URL: `/profiles/2`

**Ans Khan (ID: 3)**
- Home Image: `public/images/profiles/ans/image.png`
- Profile Banner: `public/images/profiles/ans/banner.png`
- Profile URL: `/profiles/3`

**Nazim Rauf (ID: 4)**
- Home Image: `public/images/profiles/nazim/image.png`
- Profile Banner: `public/images/profiles/nazim/banner.png`
- Profile URL: `/profiles/4`

---

## 🚀 Testing URLs

Quick links to test:

1. **Home Page** → `http://localhost:8000`
   - Check team section display

2. **Atif's Profile** → `http://localhost:8000/profiles/1`
   - Check image display, banner, and layout

3. **Waseem's Profile** → `http://localhost:8000/profiles/2`
   - Verify second person's images load

4. **Ans's Profile** → `http://localhost:8000/profiles/3`
   - Test third profile page

5. **Nazim's Profile** → `http://localhost:8000/profiles/4`
   - Verify fourth profile page

---

## 📞 Contact & Edit

**On Profile Pages You Can:**
- Click "Edit Profile" button (if you own the profile)
- Click "Contact via WhatsApp" to message them
- View other team members from same division
- See testimonials (if any)

**Edit Capabilities:**
- Update bio
- Change specializations
- Add/remove qualifications
- Update languages
- Change consultation fee
- Update contact information

---

## 🎯 What Makes It Special

✨ **Interactive Home Page**
- Team cards with hover zoom effect
- Professional image display
- Direct navigation to profiles

✨ **Digital Resume Profile Pages**
- LinkedIn-style cover image
- Professional photo centered
- Complete professional information
- Easy-to-scan card-based layout
- Testimonials section
- WhatsApp contact integration

✨ **Smart Image System**
- Automatic image discovery (based on first name)
- Multiple fallback options
- No database needed for images
- Can scale to any team size

---

## 📱 Responsive Design

**Device Viewing:**

**Mobile (< 768px):**
- Banner: 250px (reduced height)
- Profile image: 150px diameter
- Content: Single column layout
- Stats: 2-column grid

**Tablet (768px - 1024px):**
- Banner: 350px
- Profile image: 180px diameter
- Content: Single column + sidebar
- Stats: 3-column grid

**Desktop (> 1024px):**
- Banner: 400px (full size)
- Profile image: 220px diameter
- Content: 2-column + sidebar
- Stats: 4-column grid

---

## 🎬 Visual Summary

```
Home Page Flow:
Visit Home → Scroll → Team Section → 4 Team Cards
                                      ↓ (Click)
                                      ↓
Profile Page Flow:
Full Professional Resume View
├── Cover Image (Banner)
├── Profile Photo (Centered Circle)
├── Name, Title, Stats
├── About Section
├── Details (Experience, Qualifications, etc)
├── Sidebar (Fee, Contact, Team)
└── Testimonials + Edit Options
```

---

**Created:** February 6, 2026  
**Status:** ✅ Ready to Use

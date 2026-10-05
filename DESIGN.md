# Kopi Kama — Design System

> Design system resmi untuk seluruh aplikasi Dashboard Promosi Kopi Kama.
>
> Dokumen ini merupakan **single source of truth** untuk visual design,
> UI/UX, component, layout, warna, typography, spacing, dan responsive behavior.
>
> Seluruh halaman yang sudah ada maupun halaman baru WAJIB mengikuti pedoman
> yang dijelaskan dalam dokumen ini.

---

# 1. Brand Direction

## Brand

**Kopi Kama**

Tagline:

**#temankama**

Official brand asset:

`public/storage/produk/kamaijo.jpg`

---

## 1.1 Visual Character

Identitas visual Kopi Kama memiliki karakter:

- Natural
- Fresh
- Warm
- Friendly
- Modern
- Premium tetapi tidak kaku
- Organic
- Energetic
- Human
- Community-oriented

Dashboard harus terasa seperti produk digital milik brand coffee yang kuat,
bukan seperti template admin dashboard generik.

---

## 1.2 Design Keywords

Gunakan kata-kata berikut sebagai panduan ketika membuat atau memperbarui UI:

> Organic · Fresh · Friendly · Bold · Warm · Natural · Modern · Playful · Premium

Setiap keputusan visual harus mempertimbangkan karakter tersebut.

---

## 1.3 Overall Design Direction

Aplikasi menggunakan kombinasi:

- Deep green
- Kama green
- Fresh green
- Lime
- Cream
- White
- Warm neutral

Visual utama harus terasa:

- bersih
- hangat
- natural
- modern
- profesional
- memiliki karakter Kopi Kama

Gunakan whitespace dengan baik.

Premium tidak berasal dari dekorasi berlebihan,
tetapi dari:

- typography
- spacing
- hierarchy
- consistency
- proportion
- whitespace

---

## 1.4 Hindari

Jangan membuat UI yang:

- Terlihat seperti template admin generik
- Terlalu corporate
- Terlalu formal
- Terlalu banyak gradient
- Terlalu banyak warna
- Menggunakan neon secara berlebihan
- Menggunakan glassmorphism berlebihan
- Menggunakan shadow terlalu berat
- Menggunakan border radius ekstrem
- Terlalu ramai
- Menggunakan primary blue
- Menggunakan primary purple
- Menggunakan icon yang tidak konsisten
- Menggunakan typography terlalu playful untuk data
- Menggunakan dekorasi tanpa fungsi
- Mengorbankan readability demi estetika

---

# 2. Logo & Brand Asset

## Official Logo

Logo resmi Kopi Kama tersedia di:

`public/storage/produk/kamaijo.jpg`

Gunakan asset tersebut sebagai referensi visual resmi brand.

---

## 2.1 Logo Rules

Jangan:

- Membuat ulang logo menggunakan CSS
- Membuat wordmark menggunakan text
- Membuat monogram "K"
- Menggunakan icon kopi sebagai pengganti logo
- Membuat logo placeholder
- Mengubah proporsi logo
- Memutar logo
- Menambahkan efek yang mengubah identitas logo
- Menggunakan font lain untuk meniru logo

Logo harus diperlakukan sebagai brand asset.

---

## 2.2 Karakter Asset

Asset `kamaijo.jpg` merupakan artwork persegi yang mengandung:

- background hijau
- lime/kuning kehijauan
- tekstur/grain
- wordmark "kopi kama"
- tagline "#temankama"

Karena asset berbentuk JPG dan bukan logo transparan,
jangan mengecilkannya terlalu ekstrem sehingga wordmark tidak terbaca.

---

## 2.3 Penggunaan Logo

### Login

`kamaijo.jpg` dapat digunakan sebagai:

- hero visual
- brand panel
- visual identity
- background brand section

### Dashboard

Gunakan asset pada area branding dengan ukuran yang membuat
wordmark tetap terbaca.

Jika nantinya tersedia asset:

`logo.svg`

atau:

`logo-transparent.png`

maka asset transparan tersebut dapat digunakan untuk:

- sidebar
- navbar
- compact branding

Sedangkan `kamaijo.jpg` dapat tetap digunakan sebagai brand artwork.

---

# 3. Color System

Palette utama berasal dari karakter visual Kopi Kama.

---

## 3.1 Primary — Kama Green

```text
#08783F
````

 Nama:

 **Kama Green**

 Gunakan untuk:

 - primary button
- active navigation
- links
- important actions
- icon aktif
- heading accent
- brand elements
- selected state tertentu

---

 ## 3.2 Deep Green

```
#064C32
```

 Nama:

 **Deep Forest**

 Gunakan untuk:

 - sidebar
- dark section
- footer
- strong text
- hover primary
- dark brand surface

 Deep Green adalah warna paling gelap dalam brand palette.

---

 ## 3.3 Fresh Green

```
#69E77A
```

 Nama:

 **Fresh Green**

 Gunakan untuk:

 - secondary accent
- chart
- success visual tertentu
- highlight
- decorative element

 Jangan menggunakan Fresh Green sebagai warna utama seluruh halaman.

---

 ## 3.4 Kama Lime

```
#D8F56A
```

 Nama:

 **Kama Lime**

 Gunakan untuk:

 - CTA tertentu
- active navigation
- highlight
- statistic accent
- selected state
- decorative background
- visual emphasis

 Jangan menggunakan Lime untuk body text.

---

 ## 3.5 Cream

```
#FAF9F1
```

 Nama:

 **Kama Cream**

 Gunakan sebagai:

 - background utama
- page background
- section background
- authentication background

 Cream merupakan neutral utama aplikasi.

---

 ## 3.6 Lime Surface

```
#F1F8D9
```

 Gunakan untuk:

 - highlighted card
- filter area
- informational section
- secondary button
- selected background

---

 ## 3.7 White

```
#FFFFFF
```

 Gunakan untuk:

 - card
- modal
- form
- table
- navbar
- elevated surface

---

 ## 3.8 Primary Text

```
#123B2A
```

 Gunakan sebagai text utama.

---

 ## 3.9 Secondary Text

```
#4E675B
```

 Gunakan untuk:

 - description
- secondary information
- supporting text

---

 ## 3.10 Muted Text

```
#829188
```

 Gunakan untuk:

 - caption
- helper text
- metadata
- placeholder

 Jangan menggunakan muted text untuk informasi penting.

---

 ## 3.11 Border

```
#DCE5D7
```

 Gunakan sebagai border default.

 Border harus subtle.

 Hindari border hitam pekat.

---

 # 4\. Semantic Colors

 ## Success

```
Primary:
#16834D

Surface:
#E7F6ED
```

---

 ## Warning

```
Primary:
#B7791F

Surface:
#FFF6DC
```

---

 ## Danger

```
Primary:
#C24141

Surface:
#FDECEC
```

---

 ## Info

```
Primary:
#357A73

Surface:
#E8F4F1
```

---

 # 5\. Color Usage Ratio

 Gunakan prinsip:

```
60% Neutral / Cream / White
30% Green
10% Lime / Accent
```

 Green adalah identitas utama.

 Lime adalah accent.

 Jangan membuat seluruh halaman menjadi hijau terang.

---

 # 6\. Background

 Background utama:

```
#FAF9F1
```

 Gunakan background yang terasa warm dan natural.

 Dashboard tidak harus menggunakan gradient.

 Jika gradient digunakan, gunakan secara subtle.

 Contoh:

```
background:
    linear-gradient(
        135deg,
        #FAF9F1 0%,
        #F1F8D9 100%
    );
```

 Gradient tidak boleh digunakan pada semua section.

---

 # 7\. Texture

 Texture merupakan bagian dari karakter visual Kopi Kama.

 Gunakan secara terbatas.

 Texture dapat digunakan pada:

 - login hero
- branding section
- marketing section
- featured section
- empty state tertentu

 Jangan menggunakan texture pada:

 - table
- form
- dashboard data
- input
- navigation
- dense data interface

 Data interface harus tetap clean.

---

 # 8\. Typography

 ## 8.1 Branding

 Logo menggunakan official asset.

 Jangan meniru typography logo menggunakan font lain.

---

 ## 8.2 Display / Heading

 Preferred:

```
Playfair Display
```

 Fallback:

```
Georgia, serif
```

 Gunakan serif untuk:

 - hero
- major heading
- editorial section
- storytelling
- visual branding

---

 ## 8.3 Body

 Preferred:

```
Inter
```

 Fallback:

```
ui-sans-serif,
system-ui,
-apple-system,
BlinkMacSystemFont,
"Segoe UI",
sans-serif
```

 Gunakan sans-serif untuk:

 - navigation
- form
- table
- button
- data
- label
- notification
- dashboard content

---

 # 9\. Typography Scale

 ## Display

```
48px – 56px
Weight: 700
Line-height: 1.05
```

 Gunakan untuk hero atau branding besar.

---

 ## H1

```
32px – 40px
Weight: 700
Line-height: 1.15
```

---

 ## H2

```
24px – 30px
Weight: 700
Line-height: 1.2
```

---

 ## H3

```
20px – 24px
Weight: 600
Line-height: 1.25
```

---

 ## H4

```
16px – 18px
Weight: 600
Line-height: 1.3
```

---

 ## Body

```
14px – 16px
Weight: 400
Line-height: 1.6
```

---

 ## Small

```
12px – 14px
Line-height: 1.5
```

---

 # 10\. Typography Rules

 Serif digunakan secara terbatas.

 Serif:

 - hero
- major heading
- brand storytelling
- editorial section

 Sans-serif:

 - navigation
- button
- form
- table
- dashboard
- data
- label
- notification

 Jangan menggunakan serif untuk tabel atau form data.

---

 # 11\. Spacing System

 Gunakan kelipatan 4px.

```
4px   = 1
8px   = 2
12px  = 3
16px  = 4
20px  = 5
24px  = 6
32px  = 8
40px  = 10
48px  = 12
64px  = 16
80px  = 20
96px  = 24
```

 Spacing yang paling sering digunakan:

```
8px
16px
24px
32px
48px
```

---

 # 12\. Border Radius

 Gunakan radius modern tetapi tidak terlalu rounded.

 ## Small

```
6px
```

 Untuk:

 - badge
- compact control

 ## Medium

```
10px
```

 Untuk:

 - button
- input
- dropdown

 ## Large

```
14px
```

 Untuk:

 - card
- panel
- table container

 ## Extra Large

```
20px
```

 Hanya untuk:

 - hero
- featured section
- large visual section

 Jangan menggunakan `9999px` untuk card.

 Pill shape hanya untuk badge atau tag tertentu.

---

 # 13\. Shadows

 Gunakan shadow lembut dengan sedikit green tint.

 ## Subtle

```
0 1px 2px rgba(6, 76, 50, 0.05)
```

 ## Card

```
0 4px 16px rgba(6, 76, 50, 0.07)
```

 ## Elevated

```
0 12px 32px rgba(6, 76, 50, 0.10)
```

 ## Modal

```
0 20px 50px rgba(6, 76, 50, 0.16)
```

 Hindari shadow hitam pekat.

---

 # 14\. App Layout

 Struktur umum:

```
┌──────────────────────────────────────────────┐
│ Sidebar │ Navbar                            │
│         ├────────────────────────────────────┤
│         │ Page Header                        │
│         │                                    │
│         │ Main Content                       │
│         │                                    │
│         │ Cards / Tables / Forms             │
│         │                                    │
└─────────┴────────────────────────────────────┘
```

---

 # 15\. Sidebar

 Preferred background:

```
#064C32
```

 Text:

```
#FFFFFF
```

 Muted text:

```
rgba(255, 255, 255, 0.68)
```

 Active background:

```
#D8F56A
```

 Active text:

```
#064C32
```

---

 ## 15.1 Sidebar Rules

 Sidebar harus:

 - clean
- compact
- mudah dipindai
- memiliki active state jelas
- icon dan label sejajar
- spacing konsisten
- responsive

 Recommended width:

```
240px – 270px
```

 Jangan membuat sidebar terlalu lebar.

---

 # 16\. Navigation

 Navigation item:

```
Height:
40px – 44px

Padding:
10px 12px

Radius:
10px
```

 Icon:

```
18px – 20px
```

 Gap:

```
10px
```

 Active item menggunakan Kama Lime.

---

 # 17\. Navbar

 Navbar:

```
Height:
64px – 72px

Background:
#FFFFFF

Border:
1px solid #DCE5D7
```

 Navbar harus ringan.

 Jangan menggunakan shadow berat.

---

 # 18\. Page Header

 Setiap halaman harus memiliki hierarchy:

```
Page title
Short description
Primary actions
```

 Contoh:

```
Kelola Produk

Kelola seluruh produk dan informasi menu Kopi Kama.

                                + Tambah Produk
```

 Title menggunakan H1.

 Description menggunakan secondary text.

---

 # 19\. Buttons

 ## Primary

```
Background:
#08783F

Text:
#FFFFFF
```

 Hover:

```
#064C32
```

---

 ## Accent

```
Background:
#D8F56A

Text:
#064C32
```

 Gunakan untuk CTA tertentu.

---

 ## Secondary

```
Background:
#F1F8D9

Text:
#064C32
```

---

 ## Outline

```
Background:
transparent

Border:
#08783F

Text:
#08783F
```

---

 ## Danger

```
Background:
#C24141

Text:
#FFFFFF
```

---

 # 20\. Button Shape

 Default:

```
Height:
40px – 44px

Padding:
10px 16px

Radius:
10px

Font-size:
14px

Font-weight:
600
```

 Button wajib memiliki state:

 - hover
- focus
- active
- disabled
- loading jika diperlukan

 Jangan membuat style button berbeda antar halaman.

---

 # 21\. Forms

 Form harus clean dan mudah dipindai.

 Structure:

```
Label
Input
Helper text / Error
```

 Spacing:

```
Label → Input:
6px

Field → Field:
16px – 20px
```

---

 # 22\. Input

 Default:

```
Height:
42px – 46px

Background:
#FFFFFF

Border:
1px solid #DCE5D7

Radius:
10px
```

 Focus:

```
Border:
#08783F

Ring:
rgba(8, 120, 63, 0.15)
```

---

 # 23\. Input Error

 Border:

```
#C24141
```

 Error message:

```
12px – 14px
#C24141
```

 Error harus jelas tetapi tidak agresif.

---

 # 24\. Cards

 Default card:

```
Background:
#FFFFFF

Border:
1px solid #DCE5D7

Border-radius:
14px

Padding:
20px – 24px
```

 Shadow:

```
0 4px 16px rgba(6, 76, 50, 0.07)
```

---

 # 25\. Dashboard Statistic Card

 Statistic card harus memiliki hierarchy:

```
Label

Value

Supporting information
```

 Optional:

 - icon
- accent
- trend

 Contoh:

```
Total Promosi

24

+12% dari bulan lalu
```

 Gunakan Lime sebagai accent secara terbatas.

 Jangan memenuhi card dengan dekorasi.

---

 # 26\. Tables

 Table harus fokus pada readability.

 Header:

```
Background:
#F1F8D9

Color:
#064C32

Font-weight:
600
```

 Row:

```
Background:
#FFFFFF
```

 Hover:

```
Background:
#FAFCF3
```

 Border:

```
#DCE5D7
```

---

 # 27\. Table Action

 Action harus compact.

 Contoh:

```
View
Edit
Delete
```

 Gunakan icon yang konsisten.

 Delete menggunakan danger color.

 Jangan menggunakan button besar untuk setiap action.

---

 # 28\. Badge

 Default:

```
Padding:
4px 8px

Radius:
6px

Font-size:
12px

Font-weight:
600
```

---

 ## Success

```
Background:
#E7F6ED

Color:
#16834D
```

---

 ## Warning

```
Background:
#FFF6DC

Color:
#B7791F
```

---

 ## Danger

```
Background:
#FDECEC

Color:
#C24141
```

---

 ## Neutral

```
Background:
#EEF2EE

Color:
#4E675B
```

---

 # 29\. Alerts

 Alert harus dapat memiliki:

 - icon
- title
- message
- close button jika diperlukan

 Gunakan semantic colors.

 Jangan menggunakan warna terlalu saturated.

---

 # 30\. Modal

 Modal:

```
Background:
#FFFFFF

Border-radius:
14px
```

 Shadow:

```
0 20px 50px rgba(6, 76, 50, 0.16)
```

 Overlay:

```
rgba(6, 76, 50, 0.45)
```

 Structure:

```
Header
Content
Actions
```

 Modal harus responsive.

---

 # 31\. Pagination

 Pagination:

```
Height:
36px – 40px

Radius:
8px
```

 Active:

```
Background:
#08783F

Color:
#FFFFFF
```

 Hover:

```
Background:
#F1F8D9
```

---

 # 32\. Empty State

 Structure:

```
Illustration / Icon

Title

Description

Optional CTA
```

 Empty state harus terasa friendly dan sederhana.

---

 # 33\. Loading State

 Gunakan:

 - skeleton
- spinner
- progress indicator

 Gunakan warna brand:

```
#08783F
#D8F56A
```

 Jangan menggunakan spinner biru/ungu.

---

 # 34\. Authentication

 Authentication merupakan bagian penting dari brand experience.

 Login harus terasa lebih visual daripada dashboard,\
 tetapi tetap mengikuti design system.

 Recommended composition:

```
┌──────────────────────────────────────────────┐
│                                              │
│   Brand Visual          Login Form           │
│                                              │
│   Kopi Kama              Selamat datang      │
│   #temankama                                  │
│                           Email               │
│                           Password            │
│                                              │
│                           [ Masuk ]           │
│                                              │
└──────────────────────────────────────────────┘
```

 Gunakan:

 `public/storage/produk/kamaijo.jpg`

 sebagai brand visual.

 Background dapat menggunakan:

```
#FAF9F1
#D8F56A
#08783F
```

 Tetapi jangan membuat login terlalu ramai.

---

 # 35\. Dashboard Visual Hierarchy

 Prioritas visual:

```
1. Page title
2. Important KPI
3. Primary action
4. Data
5. Secondary information
```

 Jangan membuat semua element memiliki visual weight yang sama.

---

 # 36\. Charts

 Gunakan brand palette.

 Primary:

```
#08783F
```

 Secondary:

```
#69E77A
```

 Accent:

```
#D8F56A
```

 Dark:

```
#064C32
```

 Chart background:

```
transparent / #FFFFFF
```

 Grid line:

```
#E8EEE5
```

 Hindari palette random seperti:

```
blue
purple
pink
orange
red
```

 dalam satu chart kecuali memang dibutuhkan oleh data.

---

 # 37\. Iconography

 Gunakan satu icon library secara konsisten.

 Jika project sudah memiliki icon library,\
 gunakan library yang sudah tersedia.

 Jangan menambahkan library baru hanya untuk dekorasi.

 Preferred style:

```
Stroke-based
Simple
18px – 20px
```

 Icon bukan dekorasi utama.

---

 # 38\. Images

 Product image:

 - aspect ratio konsisten
- tidak terdistorsi
- `object-fit: cover` untuk thumbnail
- `object-fit: contain` jika gambar harus terlihat utuh

 Logo:

```
object-fit: contain
```

---

 # 39\. Responsive Design

 ## Desktop

```
>= 1024px
```

 Sidebar terlihat.

 Content padding:

```
24px – 32px
```

---

 ## Tablet

```
768px – 1023px
```

 Sidebar dapat:

 - collapse
- menjadi drawer

 Grid harus menyesuaikan.

---

 ## Mobile

```
< 768px
```

 Sidebar menjadi drawer/off-canvas.

 Navbar harus tetap usable.

 Card menjadi single column.

 Form menjadi single column.

 Button group dapat menjadi stacked.

 Table menggunakan horizontal scrolling jika diperlukan.

---

 # 40\. Mobile Rules

 Jangan:

 - membuat text terlalu kecil
- membuat button terlalu kecil
- membuat modal lebih lebar dari viewport
- membuat form overflow
- membuat sidebar mengambil sebagian besar layar

 Minimum touch target:

```
44px
```

---

 # 41\. Accessibility

 Semua UI harus memiliki:

 - keyboard navigation
- visible focus state
- readable contrast
- semantic HTML
- label pada form
- aria-label untuk icon-only action
- error message yang jelas
- button/link distinction

 Jangan menggunakan warna sebagai satu-satunya indikator status.

 Contoh yang salah:

```
Hijau = Active
Merah = Inactive
```

 Gunakan juga text:

```
Active
Inactive
```

---

 # 42\. Focus State

 Semua interactive element harus memiliki focus state.

 Contoh:

```
outline: 2px solid rgba(8, 120, 63, 0.25);
outline-offset: 2px;
```

 Jangan menghilangkan focus outline tanpa menyediakan pengganti yang jelas.

---

 # 43\. Animation

 Animation harus subtle.

 Recommended:

```
150ms – 250ms
ease-out
```

 Gunakan untuk:

 - hover
- dropdown
- modal
- sidebar
- button state
- notification

 Jangan menggunakan animation yang terlalu lama.

---

 # 44\. Hover

 Hover memberikan feedback visual tanpa mengubah layout.

 Contoh:

 Primary button:

```
#08783F
→
#064C32
```

 Secondary button:

```
#F1F8D9
→
slightly darker lime surface
```

 Card:

```
optional subtle elevation
```

---

 # 45\. Design Principles

 ## Principle 1 — Brand First

 UI harus langsung terasa sebagai Kopi Kama.

---

 ## Principle 2 — Clean Data

 Dashboard berisi data.

 Data harus mudah dibaca.

 Dekorasi tidak boleh mengalahkan informasi.

---

 ## Principle 3 — Organic, Not Messy

 Visual boleh organic,\
 tetapi layout harus tetap rapi.

---

 ## Principle 4 — Green Is the Brand

 Green adalah primary.

 Lime adalah accent.

 Jangan membalik proporsi tersebut.

---

 ## Principle 5 — Warm Over Cold

 Gunakan cream/warm neutral dibanding gray dingin.

---

 ## Principle 6 — Premium Through Simplicity

 Kesan premium berasal dari:

 - whitespace
- typography
- spacing
- hierarchy
- consistency

 Bukan dari:

 - shadow berlebihan
- gradient berlebihan
- animasi berlebihan

---

 ## Principle 7 — Consistency Over Decoration

 Jika harus memilih antara:

```
dekorasi
```

 dan:

```
consistency
```

 selalu prioritaskan consistency.

---

 # 46\. Do

 ## DO

 - Gunakan Kama Green sebagai primary
- Gunakan Deep Green sebagai dark surface
- Gunakan Kama Lime sebagai accent
- Gunakan Cream sebagai background
- Gunakan whitespace
- Gunakan typography yang kuat
- Gunakan card clean
- Gunakan icon konsisten
- Gunakan reusable component
- Gunakan responsive layout
- Pertahankan readability
- Gunakan official logo
- Gunakan semantic color
- Gunakan focus state
- Gunakan consistent spacing

---

 # 47\. Don't

 ## DON'T

 - Jangan membuat logo baru
- Jangan membuat monogram K
- Jangan membuat wordmark menggunakan text
- Jangan menggunakan primary blue
- Jangan menggunakan primary purple
- Jangan menggunakan gradient di semua halaman
- Jangan menggunakan shadow berat
- Jangan membuat semua card sangat rounded
- Jangan menggunakan terlalu banyak icon
- Jangan membuat setiap section memiliki warna berbeda
- Jangan membuat dashboard seperti template admin generik
- Jangan menggunakan warna random
- Jangan menggunakan texture pada data-heavy interface
- Jangan mengorbankan usability demi dekorasi

---

 # 48\. Component Consistency

 Component yang sama harus memiliki visual yang sama di seluruh aplikasi.

 Contoh:

 Jika Primary Button memiliki:

```
Height:
42px

Radius:
10px

Background:
#08783F
```

 maka seluruh halaman harus mengikuti pattern tersebut.

 Jangan membuat:

```
Dashboard button = 44px
Product button = 38px
Promotion button = 48px
```

 tanpa alasan UX yang jelas.

---

 # 49\. Reusable Components

 Jika component digunakan lebih dari satu tempat,\
 pertimbangkan membuat reusable Blade component.

 Contoh:

```
resources/views/components/
```

 Possible components:

```
button
input
select
textarea
card
badge
alert
modal
page-header
empty-state
```

 Namun jangan melakukan abstraksi berlebihan.

 Component hanya perlu dibuat reusable jika memang digunakan berulang.

---

 # 50\. CSS Architecture

 Gunakan design tokens.

 Color harus didefinisikan secara terpusat.

 Recommended:

```
:root {
    --kama-green: #08783F;
    --kama-deep-green: #064C32;
    --kama-lime: #D8F56A;
    --kama-fresh-green: #69E77A;

    --kama-cream: #FAF9F1;
    --kama-lime-surface: #F1F8D9;
    --kama-white: #FFFFFF;

    --kama-text: #123B2A;
    --kama-text-secondary: #4E675B;
    --kama-text-muted: #829188;

    --kama-border: #DCE5D7;

    --kama-success: #16834D;
    --kama-warning: #B7791F;
    --kama-danger: #C24141;
    --kama-info: #357A73;
}
```

 Jangan membuat warna baru secara sembarangan dalam component.

---

 # 51\. Implementation Rules For AI Agents

 AI coding agent WAJIB membaca `DESIGN.md`\
 sebelum melakukan perubahan UI.

 Jika membuat halaman baru:

 1. Gunakan color tokens.
2. Gunakan typography system.
3. Gunakan spacing system.
4. Gunakan component yang sudah tersedia.
5. Gunakan button style yang sudah tersedia.
6. Gunakan form style yang sudah tersedia.
7. Gunakan responsive rules.
8. Gunakan official logo.
9. Jangan membuat visual language baru.
10. Jangan membuat warna baru tanpa alasan.
11. Jangan membuat style component yang bertentangan dengan design system.
12. Pertahankan accessibility.

---

 # 52\. Functional Safety

 Redesign tidak boleh merusak functionality.

 Jangan mengubah tanpa alasan:

 - route
- controller
- model
- database
- migration
- authentication
- authorization
- validation
- business logic
- API
- query
- CRUD logic

 Perubahan UI harus fokus pada:

 - layout
- styling
- component
- responsive behavior
- visual hierarchy
- accessibility
- interaction UI

 Semua fitur yang sebelumnya bekerja harus tetap bekerja.

---

 # 53\. Final Visual Checklist

 Sebelum sebuah halaman dianggap selesai,\
 lakukan checklist berikut.

 ## Brand

 - [ ] Menggunakan warna Kopi Kama
- [ ] Menggunakan palette dari DESIGN.md
- [ ] Tidak menggunakan logo buatan sendiri
- [ ] Official logo digunakan jika diperlukan
- [ ] Tidak ada wordmark buatan CSS/text

 ## Layout

 - [ ] Spacing konsisten
- [ ] Content hierarchy jelas
- [ ] Responsive
- [ ] Sidebar konsisten
- [ ] Navbar konsisten
- [ ] Page header konsisten

 ## Typography

 - [ ] Heading hierarchy jelas
- [ ] Body text mudah dibaca
- [ ] Serif hanya digunakan pada area yang sesuai
- [ ] Font size konsisten
- [ ] Line-height nyaman

 ## Components

 - [ ] Button konsisten
- [ ] Input konsisten
- [ ] Card konsisten
- [ ] Table konsisten
- [ ] Badge konsisten
- [ ] Alert konsisten
- [ ] Modal konsisten
- [ ] Pagination konsisten
- [ ] Dropdown konsisten

 ## Colors

 - [ ] Tidak ada warna random
- [ ] Primary menggunakan Kama Green
- [ ] Lime hanya sebagai accent
- [ ] Background menggunakan warm neutral
- [ ] Semantic colors digunakan dengan benar

 ## Responsive

 - [ ] Desktop terlihat baik
- [ ] Tablet terlihat baik
- [ ] Mobile terlihat baik
- [ ] Tidak ada horizontal overflow yang tidak diperlukan
- [ ] Sidebar mobile berfungsi
- [ ] Modal mobile berfungsi
- [ ] Form mobile berfungsi
- [ ] Table mobile memiliki solusi responsive

 ## Accessibility

 - [ ] Keyboard navigation berfungsi
- [ ] Focus state terlihat
- [ ] Form memiliki label
- [ ] Icon-only button memiliki accessible label
- [ ] Contrast cukup
- [ ] Error state jelas
- [ ] Status tidak hanya bergantung pada warna

 ## UX

 - [ ] Primary action mudah ditemukan
- [ ] Hierarchy visual jelas
- [ ] Tidak terlalu banyak dekorasi
- [ ] Loading state tersedia jika diperlukan
- [ ] Empty state tersedia jika diperlukan
- [ ] Error state tersedia jika diperlukan
- [ ] Feedback setelah action jelas

 ## Code Quality

 - [ ] Tidak ada inline style yang tidak diperlukan
- [ ] Tidak ada CSS duplicate yang tidak diperlukan
- [ ] Tidak ada warna random
- [ ] Component reusable digunakan jika sesuai
- [ ] Tidak ada library baru yang tidak diperlukan
- [ ] Tidak mengubah business logic
- [ ] Tidak menghapus functionality

---

 # 54\. Golden Rule

 Jika terdapat konflik antara:

```
visual decoration
```

 dan:

```
usability + consistency + brand identity
```

 maka prioritaskan:

```
1. Usability
2. Accessibility
3. Consistency
4. Brand identity
5. Aesthetic decoration
```

 Kopi Kama harus terlihat:

 > **Fresh. Natural. Warm. Modern. Friendly. Premium.**

 Tetapi tetap:

 > **Simple. Clear. Usable. Consistent.**

---

 # 55\. Final Instruction

 `DESIGN.md` ini merupakan sumber utama untuk seluruh UI aplikasi.

 Sebelum mengubah atau membuat halaman UI:

 1. Baca `DESIGN.md`.
2. Ikuti color system.
3. Ikuti typography system.
4. Ikuti spacing system.
5. Ikuti component system.
6. Ikuti responsive rules.
7. Gunakan official brand asset.
8. Pertahankan accessibility.
9. Pertahankan functionality.
10. Jangan membuat visual language baru tanpa alasan.

 Tidak ada halaman yang boleh memiliki design language sendiri.

 Seluruh aplikasi harus terasa sebagai satu produk digital yang sama:

 # Kopi Kama

 **#temankama**

```

### Setelah ini

Setelah kamu paste dan save `DESIGN.md`, **jangan langsung menjalankan prompt redesign yang panjang sebelumnya**.

Urutannya sebaiknya:

**① `DESIGN.md` → selesai**
↓
**② Agent audit seluruh project**
↓
**③ Agent redesign layout utama**
↓
**④ Agent redesign semua view**
↓
**⑤ Build + pengecekan error**
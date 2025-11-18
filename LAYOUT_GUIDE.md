# 📐 Layout & Proportions Guide

## Desktop Layout (1024px+)

```
┌─────────────────────────────────────────────────────────────────┐
│ HEADER                                                           │
│ 🌿 Greenhouse Dashboard    [Connection] [🌙 Dark Mode]         │
│ Real-time monitoring                                            │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────┬───────────────────────────┐
│ SYSTEM OVERVIEW (66%)               │ QUICK ACTIONS (33%)       │
│ ┌─────┬─────┬─────┬─────┐          │ ┌─────────────────────┐  │
│ │🌡️  │💧  │☀️  │🌱  │          │ │ 📥 Export Data     │  │
│ │Temp │Hum │Light│Soil │          │ │ ⚙️ Threshold      │  │
│ └─────┴─────┴─────┴─────┘          │ │ 🔄 Refresh Stats  │  │
│ Actuators: 2/3                      │ └─────────────────────┘  │
└─────────────────────────────────────┴───────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│ SENSOR CARDS - Grid 4 Columns (Equal Width: 25% each)          │
├───────────────┬───────────────┬───────────────┬───────────────┤
│ SUHU UDARA    │ KELEMBAPAN    │ INTENSITAS    │ KELEMBAPAN    │
│ 🌡️ 28.5°C    │ 💧 65.2%      │ ☀️ 450 lx    │ 🌱 72.3%      │
│ Orange Card   │ Blue Card     │ Yellow Card   │ Green Card    │
│ Alert: --     │ Alert: --     │ Alert: --     │ Alert: --     │
└───────────────┴───────────────┴───────────────┴───────────────┘

┌─────────────────────────────────────────────────────────────────┐
│ STATISTICS - Grid 4 Columns                                     │
├───────────────┬───────────────┬───────────────┬───────────────┤
│ Temperature   │ Humidity      │ Light         │ Soil          │
│ Min: 22.1     │ Min: 45.3     │ Min: 250      │ Min: 55.2     │
│ Avg: 27.5     │ Avg: 62.8     │ Avg: 425      │ Avg: 68.5     │
│ Max: 32.8     │ Max: 78.4     │ Max: 650      │ Max: 82.1     │
└───────────────┴───────────────┴───────────────┴───────────────┘

┌─────────────────────────────────────────────────────────────────┐
│ HISTORICAL DATA - Grid 2x2                                      │
├───────────────────────────────┬─────────────────────────────────┤
│ 🌡️ SUHU (°C)                │ 💧 KELEMBAPAN (%)              │
│ [Chart]                       │ [Chart]                         │
│ Height: 200px                 │ Height: 200px                   │
├───────────────────────────────┼─────────────────────────────────┤
│ ☀️ CAHAYA (lx)               │ 🌱 TANAH (%)                   │
│ [Chart]                       │ [Chart]                         │
│ Height: 200px                 │ Height: 200px                   │
└───────────────────────────────┴─────────────────────────────────┘

┌─────────────────────┬───────────────────────────────────────────┐
│ KONTROL (33%)       │ LOG AKTIVITAS (66%)                       │
│ ┌─────────────────┐ │ ┌───────────────────────────────────────┐│
│ │ 💦 Pompa  [ON] │ │ │ • Pompa diaktifkan (12:30:45)        ││
│ │ 🌀 Kipas  [OFF]│ │ │ • Threshold updated (12:25:10)       ││
│ │ 💡 Lampu  [OFF]│ │ │ • Mode changed to AUTO (12:20:00)    ││
│ └─────────────────┘ │ └───────────────────────────────────────┘│
└─────────────────────┴───────────────────────────────────────────┘
```

## Tablet Layout (768px - 1024px)

```
┌─────────────────────────────────────────────┐
│ HEADER                                       │
│ 🌿 Greenhouse    [Status] [🌙]             │
└─────────────────────────────────────────────┘

┌─────────────────────────────────────────────┐
│ OVERVIEW + QUICK ACTIONS (Stacked)          │
└─────────────────────────────────────────────┘

┌─────────────────────┬───────────────────────┐
│ SENSOR CARD 1       │ SENSOR CARD 2         │
├─────────────────────┼───────────────────────┤
│ SENSOR CARD 3       │ SENSOR CARD 4         │
└─────────────────────┴───────────────────────┘

┌─────────────────────┬───────────────────────┐
│ STAT 1 & 2          │ STAT 3 & 4            │
└─────────────────────┴───────────────────────┘

┌─────────────────────────────────────────────┐
│ CHART 1                                     │
├─────────────────────────────────────────────┤
│ CHART 2                                     │
├─────────────────────────────────────────────┤
│ CHART 3                                     │
├─────────────────────────────────────────────┤
│ CHART 4                                     │
└─────────────────────────────────────────────┘

┌─────────────────────────────────────────────┐
│ CONTROLS                                    │
├─────────────────────────────────────────────┤
│ LOGS                                        │
└─────────────────────────────────────────────┘
```

## Mobile Layout (<768px)

```
┌───────────────────────────┐
│ HEADER                    │
│ 🌿 Greenhouse            │
│ [Status] [🌙]            │
└───────────────────────────┘

┌───────────────────────────┐
│ OVERVIEW                  │
│ Mini cards                │
└───────────────────────────┘

┌───────────────────────────┐
│ QUICK ACTIONS (Stack)     │
└───────────────────────────┘

┌───────────────────────────┐
│ 🌡️ SUHU                 │
│ 28.5°C                    │
└───────────────────────────┘

┌───────────────────────────┐
│ 💧 KELEMBAPAN            │
│ 65.2%                     │
└───────────────────────────┘

┌───────────────────────────┐
│ ☀️ CAHAYA                │
│ 450 lx                    │
└───────────────────────────┘

┌───────────────────────────┐
│ 🌱 TANAH                 │
│ 72.3%                     │
└───────────────────────────┘

┌───────────────────────────┐
│ STATISTICS (Stack)        │
│ Each sensor stacked       │
└───────────────────────────┘

┌───────────────────────────┐
│ CHART 1                   │
├───────────────────────────┤
│ CHART 2                   │
├───────────────────────────┤
│ CHART 3                   │
├───────────────────────────┤
│ CHART 4                   │
└───────────────────────────┘

┌───────────────────────────┐
│ CONTROLS                  │
├───────────────────────────┤
│ LOGS                      │
└───────────────────────────┘
```

## Spacing & Sizing Guidelines

### Gaps (Tailwind Classes)

- Section gaps: `gap-6` (24px)
- Card gaps: `gap-4` (16px)
- Element gaps: `gap-2` or `gap-3` (8px or 12px)

### Padding

- Large cards: `p-5` (20px)
- Small cards: `p-4` (16px)
- Compact elements: `p-3` (12px)

### Border Radius

- Cards: `rounded-lg` (16px) or `rounded-xl` (20px)
- Buttons: `rounded-lg` (12px)
- Inputs: `rounded-lg` (8px)

### Font Sizes

- Page title: `text-3xl` or `text-4xl` (30-36px)
- Section headers: `text-lg` (18px)
- Card titles: `text-sm` (14px)
- Values: `text-3xl` or `text-4xl` (30-36px)
- Subtitles: `text-xs` (12px)

### Colors

- Temperature: Orange (#f97316)
- Humidity: Blue (#3b82f6)
- Light: Yellow (#facc15)
- Soil: Green (#10b981)
- Danger: Red (#ef4444)
- Warning: Amber (#f59e0b)

## Grid System

### Desktop (lg:)

- 4 columns: `grid-cols-1 lg:grid-cols-4`
- 3 columns: `grid-cols-1 lg:grid-cols-3`
- 2 columns: `grid-cols-1 lg:grid-cols-2`

### Tablet (md:)

- 2 columns: `grid-cols-1 md:grid-cols-2`

### Mobile

- 1 column: `grid-cols-1`

## Chart Sizing

### Desktop

- Container: 2x2 grid
- Each chart: ~50% width, 200px height
- Max height: 280px

### Tablet

- Stack 2 per row
- Height: 200px

### Mobile

- Full width stack
- Height: 200px

## Card Proportions

### Sensor Cards

- Icon size: 48px (w-12 h-12)
- Value font: 36px (text-4xl)
- Subtitle: 12px (text-xs)

### Control Cards

- Icon size: 32-36px (w-8 h-8 to w-9 h-9)
- Button width: 60px minimum
- Button height: 36px

### Overview Cards

- Mini sensor: 24px icon
- Value: 14px font
- 4-column grid on desktop

## Z-Index Layers

1. Base content: z-0
2. Cards: z-10
3. Modals: z-1000
4. Toast: z-9999
5. Alerts: z-50

## Dark Mode Color Adjustments

### Light Mode

- Background: Gradient green-50 → white → pink-50
- Cards: white
- Text: gray-800
- Borders: gray-300

### Dark Mode

- Background: Gradient gray-900 → gray-800
- Cards: gray-800
- Text: gray-200
- Borders: gray-600

## Responsive Breakpoints

```css
/* Tailwind Breakpoints */
sm: 640px   → Small tablets
md: 768px   → Tablets
lg: 1024px  → Laptops
xl: 1280px  → Desktops
2xl: 1536px → Large desktops
```

## Animation Timings

- Quick: 150ms
- Normal: 300ms
- Slow: 500ms
- Chart updates: 0ms (instant)

## Best Practices Applied

✅ Consistent spacing (4px grid system)
✅ Proper visual hierarchy
✅ Balanced proportions
✅ Adequate white space
✅ Responsive breakpoints
✅ Accessible touch targets (44px minimum)
✅ Readable font sizes (14px minimum)
✅ Color contrast (WCAG AA compliant)
✅ Smooth transitions
✅ Performance optimized

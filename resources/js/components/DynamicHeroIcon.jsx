// DynamicHeroIcon.jsx
import React from 'react'
import * as OutlineIcons from '@heroicons/react/24/outline'
import * as SolidIcons  from '@heroicons/react/24/solid'

/**
 * @param {{ iconString: string, className?: string, width?: number|string, height?: number|string }} props
 *   iconString: format "heroicon-{styleChar}-{icon-name-kebab}", 
 *               contohnya "heroicon-c-chart-bar-square"
 */
export default function DynamicHeroIcon({ iconString, ...props }) {
  const parts = iconString.split('-')         // ["heroicon","c","chart","bar","square"]
  if (parts.length < 3 || parts[0] !== 'heroicon') {
    console.warn(`[DynamicHeroIcon] format iconString "${iconString}" tidak valid`)
    return null
  }

  const [, styleChar, ...iconParts] = parts   // styleChar="c", iconParts=["chart","bar","square"]

  // Map kode 'c' → Outline, 's' → Solid (bisa kamu sesuaikan)
  const lib = styleChar === 's' 
    ? SolidIcons 
    : OutlineIcons   // default untuk 'c' atau lainnya

  // Ubah ["chart","bar","square"] → "ChartBarSquareIcon"
  const pascalName = iconParts
    .map(w => w.charAt(0).toUpperCase() + w.slice(1))
    .join('') + 'Icon'

  const IconComp = lib[pascalName]
  if (!IconComp) {
    console.warn(`[DynamicHeroIcon] Komponen "${pascalName}" tidak ditemukan di ${styleChar === 's' ? 'solid' : 'outline'}`)
    return null
  }

  return <IconComp {...props} />
}

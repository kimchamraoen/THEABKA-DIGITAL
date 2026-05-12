<svg xmlns="http://www.w3.org/2000/svg" role="presentation" style="display: none" aria-hidden="true" focusable="false">
    {{-- Kept for potential future DOM-element usage; not used on pseudo-elements --}}
    <filter id="glass-distortion" x="-5%" y="-5%" width="110%" height="110%" filterUnits="objectBoundingBox">
        <feTurbulence type="fractalNoise" baseFrequency="0.015 0.015" numOctaves="2" seed="5" result="turbulence" />
        <feGaussianBlur in="turbulence" stdDeviation="6" result="softMap" />
        <feDisplacementMap in="SourceGraphic" in2="softMap" scale="12" xChannelSelector="R" yChannelSelector="G" />
    </filter>

    <filter id="glass-distortion-soft" x="-5%" y="-5%" width="110%" height="110%" filterUnits="objectBoundingBox">
        <feTurbulence type="fractalNoise" baseFrequency="0.015 0.015" numOctaves="2" seed="5" result="turbulence" />
        <feGaussianBlur in="turbulence" stdDeviation="6" result="softMap" />
        <feDisplacementMap in="SourceGraphic" in2="softMap" scale="12" xChannelSelector="R" yChannelSelector="G" />
    </filter>

    <filter id="glass-distortion-strong" x="-5%" y="-5%" width="110%" height="110%" filterUnits="objectBoundingBox">
        <feTurbulence type="fractalNoise" baseFrequency="0.008 0.012" numOctaves="2" seed="17" result="turbulence" />
        <feGaussianBlur in="turbulence" stdDeviation="5" result="softMap" />
        <feDisplacementMap in="SourceGraphic" in2="softMap" scale="20" xChannelSelector="R" yChannelSelector="G" />
    </filter>
</svg>

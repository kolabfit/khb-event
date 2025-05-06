import React from 'react';

export default function HeroSection() {
  return (
    <section className="relative overflow-hidden bg-gradient-to-r from-purple-700 via-purple-600 to-green-500 mb-6">
      <div className="container mx-auto px-4 py-16">
        <div className="flex flex-col md:flex-row items-center">
          {/* Left Column */}
          <div className="w-full md:w-1/2 text-white">
            <h1 className="text-5xl md:text-6xl font-bold mb-4 drop-shadow-lg">
              Jelajahi Berbagai Event Menarik
            </h1>
            <p className="text-lg md:text-xl mb-8 drop-shadow">
              Temukan dan beli tiket event sesuai minatmu, dari konser hingga workshop.
            </p>
            <div className="flex flex-wrap gap-4">
              <button className="px-6 py-3 bg-green-300 text-black font-semibold rounded-md hover:bg-green-400 transition">
                Jelajahi Event
              </button>
              <button className="px-6 py-3 bg-white text-purple-700 font-semibold rounded-md hover:bg-gray-100 transition">
                Buat Event Sekarang
              </button>
            </div>
          </div>
        </div>
      </div>
      {/* Optional: decorative SVG or shape */}
      <div className="absolute inset-0 pointer-events-none">
        <svg
          className="absolute bottom-0 right-0 w-64 opacity-20"
          viewBox="0 0 600 600"
          fill="none"
        >
          <circle cx="300" cy="300" r="300" fill="#ffffff" />
        </svg>
      </div>
    </section>
  );
}

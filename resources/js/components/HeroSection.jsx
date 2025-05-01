import React from 'react';

export default function HeroSection() {
  return (
    <div className="flex justify-center w-full py-8">
      <div className="w-full max-w-screen-2xl px-4 mx-auto">
        <div className="w-full bg-purple-700 rounded-lg overflow-hidden">
          <div className="flex flex-col md:flex-row items-start justify-between px-14 py-14">
          {/* Left Column */}
          <div className="w-full md:w-1/2 text-white mb-8 md:mb-0">
            <h1 className="text-5xl font-bold mb-4">
              Jelajahi Berbagai Event Menarik
            </h1>
            <p className="text-xl mb-8">
              Temukan dan beli tiket event sesuai minatmu
            </p>
            <div className="flex flex-wrap gap-4">
              <button className="px-6 py-3 bg-green-300 text-black font-medium rounded-md hover:bg-green-400 transition-colors">
                Jelajahi Event
              </button>
              <button className="px-6 py-3 bg-white text-black font-medium rounded-md hover:bg-gray-100 transition-colors">
                Buat Event Sekarang
              </button>
            </div>
          </div>

          {/* Right Column */}
          <div className="w-full md:w-5/12 bg-purple-600/60 rounded-lg p-6 text-white">
            <h2 className="text-2xl font-semibold mb-4">
              Tipks Mengisa Event yang Sukses
            </h2>
            <ul className="space-y-4 mb-6">
              <li className="flex items-start">
                <div className="w-3 h-3 mt-2 mr-3 rounded-full bg-white"></div>
                <span>Begaimana cara menjual tiket lebih cepat?</span>
              </li>
              <li className="flex items-start">
                <div className="w-3 h-3 mt-2 mr-3 rounded-full bg-white"></div>
                <span>2 Strategi promost event</span>
              </li>
            </ul>
            <a href="#" className="text-lg hover:underline">
              Lihat Semua Artikel!
            </a>
          </div>
        </div>
      </div>
    </div>
    </div>
  );
}
import React from 'react';

export default function Navbar() {
  return (
    <div className="flex justify-center w-full bg-white shadow-sm">
      <div className="w-full max-w-screen-2xl px-4 mx-auto">
        <nav className="flex items-center justify-between py-3">
          {/* Logo */}
          <div className="flex items-center">
            <div className="flex items-center justify-center w-10 h-10 mr-2 bg-green-400 rounded-md">
              <svg xmlns="http://www.w3.org/2000/svg" className="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
              </svg>
            </div>
            <span className="text-xl font-bold text-indigo-900">KHB EVENT</span>
          </div>

          {/* Search Bar */}
          <div className="flex items-center px-3 py-2 mx-4 bg-gray-100 rounded-md flex-grow max-w-md">
            <svg xmlns="http://www.w3.org/2000/svg" className="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input 
              type="text" 
              placeholder="Cari event" 
              className="w-full px-2 py-1 ml-2 bg-transparent outline-none text-gray-600"
            />
          </div>

          {/* Navigation Items */}
          <div className="flex items-center space-x-6">
            <button className="font-medium text-gray-800 hover:text-indigo-900">Kategori</button>
            <button className="font-medium text-gray-800 hover:text-indigo-900">Buat Event</button>
            <button className="font-medium text-gray-800 hover:text-indigo-900">Bantuan</button>
            <button className="px-5 py-2 font-medium text-black bg-green-300 rounded-full hover:bg-green-400">Login</button>
          </div>
        </nav>
      </div>
    </div>
  );
}
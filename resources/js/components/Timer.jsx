import React from 'react';
import { Clock } from 'lucide-react';

export default function Timer() {
  return (
    <div className="flex justify-center w-full py-2">
      <div className="w-full max-w-7xl px-4 mx-auto">
        <div className="w-full bg-green-50 rounded-lg">
          <div className="flex items-center justify-between px-6 py-4">
            <div className="flex items-center space-x-3">
              <Clock className="w-6 h-6 text-green-600" />
              <span className="text-xl font-medium text-gray-800">
                Waktu tersisa: <span className="font-semibold">2 Hari, 7 jam, 45 Menit</span>
              </span>
            </div>
            
            <a href="#" className="flex items-center text-green-600 hover:text-green-700">
              <span className="font-medium">Lihat Detail</span>
              <svg xmlns="http://www.w3.org/2000/svg" className="w-5 h-5 ml-1" viewBox="0 0 20 20" fill="currentColor">
                <path fillRule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clipRule="evenodd" />
              </svg>
            </a>
          </div>
        </div>
      </div>
    </div>
  );
}
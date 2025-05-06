import React from 'react';

const EventTips = () => {
  const tips = [
    'Bagaimana cara menjual tiket lebih cepat?',
    '7-strategi promosi event'
  ];

  return (
    <div className="w-1/2 p-4 bg-gray-50 rounded-lg">
      <h2 className="text-xl font-bold mb-3">Tips Mengelola Event yang Sukses</h2>
      
      <ul className="mb-3">
        {tips.map((tip, index) => (
          <li key={index} className="flex items-start mb-2">
            <span className="text-green-500 text-lg mr-2">•</span>
            <span>{tip}</span>
          </li>
        ))}
      </ul>
      
      <a href="#" className="text-green-600 font-medium hover:underline">
        Lihat Semua Artikel
      </a>
    </div>
  );
};

export default EventTips;
import React from 'react';
import { Music, Monitor, PartyPopper, Mic, Scissors, Calendar } from 'lucide-react';

const EventPopuler = () => {
  const eventCategories = [
    { name: 'Musik', icon: <Music className="w-6 h-6 text-purple-600" /> },
    { name: 'Seminar', icon: <Monitor className="w-6 h-6 text-green-500" /> },
    { name: 'Festival', icon: <PartyPopper className="w-6 h-6 text-green-500" /> },
    { name: 'Komedi', icon: <Mic className="w-6 h-6 text-purple-600" /> },
    { name: 'Workshop', icon: <Scissors className="w-6 h-6 text-purple-800" /> },
    { name: 'Lainnya', icon: <Calendar className="w-6 h-6 text-green-500" /> },
  ];

  return (
    <div className="w-1/2 p-4">
      <h2 className="text-2xl font-bold mb-4">Event Populer</h2>
      <div className="grid grid-cols-6 gap-2">
        {eventCategories.map((category, index) => (
          <div key={index} className="flex flex-col items-center">
            <div className="rounded-full bg-gray-100 p-3 mb-2">
              {category.icon}
            </div>
            <span className="text-sm">{category.name}</span>
          </div>
        ))}
      </div>
    </div>
  );
};

export default EventPopuler;
import React, { useState } from 'react';
import { Music, Monitor, PartyPopper, Mic, Scissors, Calendar } from 'lucide-react';
import EventCards from './EventCard';

const App = () => {
  const [email, setEmail] = useState('');

  const handleSubscribe = () => {
    console.log('Subscribing email:', email);
    setEmail('');
    alert('Terima kasih telah berlangganan!');
  };

  const eventCategories = [
    { name: 'Musik', icon: <Music className="w-6 h-6 text-purple-600" /> },
    { name: 'Seminar', icon: <Monitor className="w-6 h-6 text-green-500" /> },
    { name: 'Festival', icon: <PartyPopper className="w-6 h-6 text-green-500" /> },
    { name: 'Komedi', icon: <Mic className="w-6 h-6 text-purple-600" /> },
    { name: 'Workshop', icon: <Scissors className="w-6 h-6 text-purple-800" /> },
    { name: 'Lainnya', icon: <Calendar className="w-6 h-6 text-green-500" /> },
  ];

  const tips = [
    'Bagaimana cara menjual tiket lebih cepat?',
    '7-strategi promosi event'
  ];

  return (
    <div className="container mx-auto px-4 py-6">
      {/* Main Content Area */}
      <div className="flex flex-wrap">
        {/* Left Column (60%) */}
        <div className="w-full">
          {/* Event Populer Section */}
          <div className="mb-8">
            <h2 className="text-xl font-bold mb-4">Event Populer</h2>
            <div className="grid grid-cols-6 gap-2 mb-6">
              {eventCategories.map((category, index) => (
                <a
                  key={index}
                  href="#"
                  className="flex flex-col items-center cursor-pointer group"
                  onClick={(e) => {
                    e.preventDefault();
                    console.log(`Clicked on ${category.name}`);
                  }}
                >
                  <div className="rounded-full bg-gray-100 p-3 mb-2 group-hover:bg-gray-200 transition-colors">
                    {category.icon}
                  </div>
                  <span className="text-xs group-hover:font-medium transition-all">{category.name}</span>
                </a>
              ))}
            </div>
          </div>

          {/* Event Cards */}
          <div className="grid grid-cols-4 gap-7">
            <EventCards />
          </div>
        </div>
      </div>

      {/* Tombol Jelajah Lebih Banyak */}
      <div className="w-full flex justify-center mt-10">
        <button
          onClick={() => console.log('Jelajah ke lebih banyak event')}
          className="
      bg-transparent 
      border-2 border-purple-600 
      text-purple-600 
      px-6 py-2 
      rounded-lg 
      hover:bg-purple-600 hover:text-white 
      transition
      focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2
    "
        >
          Jelajah ke Lebih Banyak Event
        </button>
      </div>




    </div>
  );
};

export default App;
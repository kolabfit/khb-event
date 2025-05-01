import React, { useState } from 'react';
import { Music, Monitor, PartyPopper, Mic, Scissors, Calendar } from 'lucide-react';

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

  const events = [
    {
      id: 1,
      image: "/api/placeholder/400/240",
      date: "30 April",
      title: "Konser Musik Live",
      location: "Jakarta",
      price: "Rp 150.000"
    },
    {
      id: 2,
      image: "/api/placeholder/400/240",
      date: "5 Mei 2024",
      title: "Webinar Digital Marketing",
      location: "Online",
      price: "Rp 300.000"
    },
    {
      id: 3,
      image: "/api/placeholder/400/240",
      date: "10 Jun",
      title: "Festival Kuliner Nusantara",
    //   subtitle: "Nusantara",
      location: "Surabaya",
      price: "Rp 350.000"
    }
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
        <div className="w-3/5 pr-8">
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
          <div className="grid grid-cols-3 gap-4">
            {events.map((event) => (
              <a 
                key={event.id} 
                href="#" 
                className="flex flex-col group cursor-pointer hover:shadow-md transition-shadow rounded-lg p-2"
                onClick={(e) => {
                  e.preventDefault();
                  console.log(`Clicked on event: ${event.title}`);
                }}
              >
                <div className="rounded-lg overflow-hidden mb-2 relative">
                  <img 
                    src={event.image}
                    alt={event.title}
                    className="w-full h-32 object-cover group-hover:scale-105 transition-transform"
                  />
                  <div className="absolute inset-0 bg-black opacity-0 group-hover:opacity-10 transition-opacity"></div>
                </div>
                <div className="bg-green-100 text-green-800 text-xs px-3 py-1 rounded-full w-fit mb-1">
                  {event.date}
                </div>
                <h3 className="font-bold text-base leading-tight group-hover:text-green-600 transition-colors">{event.title}</h3>
                {event.subtitle && (
                  <h4 className="font-bold text-base leading-tight">{event.subtitle}</h4>
                )}
                <p className="text-sm text-gray-600 mt-1">{event.location}</p>
                <p className="font-medium mt-1">{event.price}</p>
              </a>
            ))}
          </div>
        </div>

        {/* Right Column (40%) */}
        <div className="w-2/5">
          {/* Tips Section */}
          <div className="bg-gray-50 rounded-lg p-4 mb-6">
            <h2 className="text-lg font-bold mb-3">Tips Mengelola Event yang Sukses</h2>
            
            <ul className="mb-3">
              {tips.map((tip, index) => (
                <li key={index} className="flex items-start mb-2">
                  <span className="text-green-500 text-lg mr-2 flex-shrink-0">•</span>
                  <span className="text-sm">{tip}</span>
                </li>
              ))}
            </ul>
            
            <a href="#" className="text-green-600 text-sm font-medium hover:underline">
              Lihat Semua Artikel
            </a>
          </div>

          {/* Testimonial Card */}
          <div className="bg-gray-50 rounded-lg p-4">
            <h2 className="text-lg font-bold mb-3 flex items-center">
              <div className="bg-purple-100 rounded-full p-3 mr-3 flex-shrink-0">
                <div className="flex items-center justify-center">
                  <div className="bg-purple-800 w-2 h-6 mr-1"></div>
                  <div className="bg-purple-500 w-2 h-4"></div>
                </div>
              </div>
              Testimonial
            </h2>
            
            <div className="pl-12">
              <p className="text-base font-medium mb-2 max-w-[400px]">
                KHB Event bantu kami menjual 300+ tiket dalam seminggu
              </p>
              <p className="text-sm text-gray-600">
                - Event Organizer Jakarta
              </p>
            </div>
          </div>
        </div>
      </div>

      {/* Footer Stats & Subscribe */}
      <div className="border-t border-gray-200 mt-12 pt-8">
        <div className="flex flex-wrap items-center justify-between">
          {/* Stats Section */}
          <div className="flex space-x-12">
            <div className="text-center">
              <h2 className="text-4xl font-bold">1.542</h2>
              <p className="text-sm text-gray-600">Total Event Aktif</p>
            </div>
            
            <div className="text-center">
              <h2 className="text-4xl font-bold">25.390</h2>
              <p className="text-sm text-gray-600">Tiket Terjual</p>
            </div>
            
            <div className="text-center">
              <h2 className="text-4xl font-bold">320+</h2>
              <p className="text-sm text-gray-600">EO Terdaftar</p>
            </div>
          </div>
          
          {/* Subscribe Section */}
          <div className="w-80">
            <h3 className="font-medium mb-3">
              Dapatkan info event menarik langsung isi emailmu!
            </h3>
            
            <div>
              <input
                type="email"
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                placeholder="Masukkan email kamu"
                className="w-full p-2 border border-gray-300 rounded-md mb-2"
              />
              
              <button
                onClick={handleSubscribe}
                className="w-full bg-green-300 hover:bg-green-400 py-2 px-4 rounded-md font-medium transition-colors"
              >
                Berlangganan
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default App;
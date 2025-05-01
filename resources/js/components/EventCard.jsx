import React from 'react';

const EventCards = () => {
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
      price: "Rp 3000.000"
    },
    {
      id: 3,
      image: "/api/placeholder/400/240",
      date: "10 Jun",
      title: "Festival Kuliner Nusantara",
      location: "Padang",
      price: "Rp 250.000"
    }
  ];

  return (
    <div className="w-1/2 p-4">
      <div className="grid grid-cols-3 gap-4">
        {events.map((event) => (
          <div key={event.id} className="flex flex-col">
            <div className="rounded-lg overflow-hidden mb-2">
              <img 
                src={event.image}
                alt={event.title}
                className="w-full h-32 object-cover"
              />
            </div>
            <div className="bg-green-100 text-green-800 text-xs px-3 py-1 rounded-full w-fit mb-1">
              {event.date}
            </div>
            <h3 className="font-bold text-base leading-tight">{event.title}</h3>
            <p className="text-sm text-gray-600 mt-1">{event.location}</p>
            <p className="font-medium mt-2">{event.price}</p>
          </div>
        ))}
      </div>
    </div>
  );
};

export default EventCards;
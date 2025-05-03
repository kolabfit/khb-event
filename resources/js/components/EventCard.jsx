import React from 'react';

const EventCards = ({ dataevent }) => {
  const events = [
    {
      id: 1,
      image: 'http://127.0.1:8000/image/1.jpeg',
      date: '30 April',
      title: 'Konser Musik Livkkk',
      location: 'Jakarta',
      price: 'Rp 150.000',
      organizerName: 'EO Musik Nusantara',
      organizerImage: 'http://127.0.1:8000/image/1.jpeg',
    },
    {
      id: 2,
      image: 'http://127.0.1:8000/image/1.jpeg',
      date: '5 Mei 2024',
      title: 'Webinar Digital Marketing',
      location: 'Online',
      price: 'Rp 300.000',
      organizerName: 'EO Musik Nusantara',
      organizerImage: 'http://127.0.1:8000/image/1.jpeg',
    },
    {
      id: 3,
      image: 'http://127.0.1:8000/image/1.jpeg',
      date: '10 Jun',
      title: 'Festival Kuliner Nusantara',
      location: 'Padang',
      price: 'Rp 250.000',
      organizerName: 'EO Musik Nusantara',
      organizerImage: 'http://127.0.1:8000/image/1.jpeg',
    },
    {
      id: 4,
      image: 'http://127.0.1:8000/image/1.jpeg',
      date: '30 April',
      title: 'Konser Musik Live',
      location: 'Jakarta',
      price: 'Rp 150.000',
      organizerName: 'EO Musik Nusantara',
      organizerImage: 'http://127.0.1:8000/image/1.jpeg',
    },
    {
      id: 5,
      image: 'http://127.0.1:8000/image/1.jpeg',
      date: '5 Mei 2024',
      title: 'Webinar Digital Marketing',
      location: 'Online',
      price: 'Rp 300.000',
      organizerName: 'EO Musik Nusantara',
      organizerImage: 'http://127.0.1:8000/image/1.jpeg',
    },
    {
      id: 6,
      image: 'http://127.0.1:8000/image/1.jpeg',
      date: '10 Jun',
      title: 'Festival Kuliner Nusantara',
      location: 'Padang',
      price: 'Rp 250.000',
      organizerName: 'EO Musik Nusantara',
      organizerImage: 'http://127.0.1:8000/image/1.jpeg',
    },
    {
      id: 7,
      image: 'http://127.0.1:8000/image/1.jpeg',
      date: '30 April',
      title: 'Konser Musik Live',
      location: 'Jakarta',
      price: 'Rp 150.000',
      organizerName: 'EO Musik Nusantara',
      organizerImage: 'http://127.0.1:8000/image/1.jpeg',
    },
    {
      id: 8,
      image: 'http://127.0.1:8000/image/1.jpeg',
      date: '5 Mei 2024',
      title: 'Webinar Digital Marketing',
      location: 'Online',
      price: 'Rp 300.000',
      organizerName: 'EO Musik Nusantara',
      organizerImage: 'http://127.0.1:8000/image/1.jpeg',
    },
  ];

  const handleDetail = (event) => {
    // Ganti dengan navigasi React Router atau sejenis bila perlu
    console.log(`Lihat detail event: ${event.title}`);
  };

  return (
    <>
      {dataevent.map((event) => (
        // Card wrapper: rounded & overflow-hidden, tanpa padding
        <div
          key={event.id}
          className="group cursor-pointer shadow-md transition-shadow rounded-lg bg-white overflow-hidden"
        >
          {/* Gambar full‐width, membulat di atas */}
          <div className="relative">
            <img
              src={event.thumbnail}
              alt={event.title}
              className="w-full h-62 object-cover group-hover:scale-105 transition-transform"
            />
            <div className="absolute inset-0 bg-black opacity-0 group-hover:opacity-10 transition-opacity" />
          </div>

          {/* Konten: beri padding di sini */}
          <div className="p-4 flex flex-col">
            <div className="self-start inline-block bg-green-100 text-green-800 text-xs px-3 py-1 rounded-full mb-2">
              {new Date(event.start_date).toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'long',
                year: 'numeric',
              })}
            </div>
            <h3 className="font-bold text-base leading-tight group-hover:text-green-600 transition-colors">
              {event.title}
            </h3>
            <p className="text-sm text-gray-600 mt-1">{event.location}</p>
            <p className="font-medium mt-1 mb-2">{new Intl.NumberFormat('id-ID', {
              style: 'currency',
              currency: 'IDR',
              minimumFractionDigits: 0,
            }).format(event.price)}</p>

            {/* Garis Pemisah */}
            <div className="my-3 border-t border-gray-200" />

            {/* Organizer (EO) */}
            <div className="flex items-center mt-2 mb-2">
              <img
                // src={event.organizerImage}
                alt={event.user.name}
                className="w-7 h-7 rounded-full object-cover"
              />
              <span className="ml-2 text-sm text-gray-700">{event.user.name}</span>
            </div>

            {/* <button
              onClick={() => handleDetail(event)}
              className="mt-4 w-full text-center px-4 py-2 bg-green-500 text-white text-sm font-medium rounded-md hover:bg-green-600 transition-colors"
            >
              Lihat Detail
            </button> */}
          </div>
        </div>
      ))}
    </>
  );
};

export default EventCards;

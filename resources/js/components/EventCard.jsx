// resources/js/Pages/EventCards.jsx
import React from 'react';
import { Link } from '@inertiajs/react';

const EventCards = ({ dataevent }) => {
  return (
    <>
      {dataevent.map((event) => {
        // Ambil angka bersih atau 0 jika null
        const rawPrice = event.price != null ? Number(event.price) : 0;
        // Label harga: jika >0 format rupiah, kalau 0 tulis "Gratis"
        const priceLabel =
          rawPrice > 0
            ? rawPrice.toLocaleString('id-ID', {
              style: 'currency',
              currency: 'IDR',
              minimumFractionDigits: 0,
            })
            : 'Gratis';

        return (
          <Link
            href={route('detail-events', { id: event.id })}
            key={event.id}
          >
            <div className="group cursor-pointer shadow-md transition-shadow rounded-lg bg-white overflow-hidden">
              {/* Gambar */}
              <div className="relative">
                <img
                  src={event.thumbnail}
                  alt={event.title}
                  className="w-full h-48 object-cover group-hover:scale-105 transition-transform"
                />
                <div className="absolute inset-0 bg-black opacity-0 group-hover:opacity-10 transition-opacity" />
              </div>

              {/* Konten */}
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

                {/* Harga / Gratis */}
                <p className="text-lg font-semibold text-gray-800 mt-1 mb-2">
                  {priceLabel}
                </p>

                <div className="my-3 border-t border-gray-200" />

                {/* Organizer */}
                <div className="flex items-center justify-between mt-2 mb-2">
                  <div className="flex items-center">
                    <img
                      src={event.user.avatar}
                      alt={event.user.name}
                      className="w-7 h-7 rounded-full object-cover"
                    />
                    <span className="ml-2 text-sm text-gray-700">
                      {event.user.name}
                    </span>
                  </div>
                  <span className="text-sm text-gray-500">
                    Dibuat {new Date(event.created_at).toLocaleDateString('id-ID', {
                      day: 'numeric',
                      month: 'long',
                      year: 'numeric'
                    })}
                  </span>
                </div>
              </div>
            </div>
          </Link>
        );
      })}
    </>
  );
};

export default EventCards;

// resources/js/Pages/EventCards.jsx
import React from 'react';
import { Link } from '@inertiajs/react';

const EventCards = ({ dataevent = [] }) => {
  if (!dataevent || dataevent.length === 0) {
    return (
      <div className="col-span-full text-center py-8 text-gray-500">
        Tidak ada event yang tersedia
      </div>
    );
  }

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

        const eventStartDate = new Date(event.start_date);
        const now = new Date();
        const isPastEvent = eventStartDate < now;

        return (
          <Link
            href={route('detail-events', { id: event.id })}
            key={event.id}
            className="block h-full"
          >
            <div className="group cursor-pointer shadow-md transition-shadow rounded-lg bg-white overflow-hidden h-full flex flex-col">
              {/* Gambar */}
              <div className="relative aspect-[4/3]">
                <img
                  src={event.thumbnail}
                  alt={event.title}
                  className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                />
                <div className="absolute inset-0 bg-black opacity-0 group-hover:opacity-10 transition-opacity" />
                {isPastEvent && (
                  <div className="absolute top-2 left-2 bg-red-600 text-white text-xs px-2 py-1 rounded-md">
                    Event Telah Berakhir
                  </div>
                )}
              </div>

              {/* Konten */}
              <div className="p-3 sm:p-4 flex flex-col flex-grow">
                <div className="self-start inline-block bg-green-100 text-green-800 text-xs px-2 sm:px-3 py-1 rounded-full mb-2">
                  {new Date(event.start_date).toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'long',
                    year: 'numeric',
                  })}
                </div>
                <h3 className="font-bold text-sm sm:text-base leading-tight group-hover:text-green-600 transition-colors line-clamp-2">
                  {event.title}
                </h3>
                <p className="text-xs sm:text-sm text-gray-600 mt-1 line-clamp-1">{event.location}</p>

                {/* Harga / Gratis */}
                <p className="text-base sm:text-lg font-semibold text-gray-800 mt-1 mb-2">
                  {priceLabel}
                </p>

                <div className="my-2 sm:my-3 border-t border-gray-200" />

                {/* Organizer */}
                <div className="flex items-center justify-between mt-auto">
                  <div className="flex items-center">
                    <img
                      src={event.user?.avatar}
                      alt={event.user?.name}
                      className="w-6 h-6 sm:w-7 sm:h-7 rounded-full object-cover"
                    />
                    <span className="ml-2 text-xs sm:text-sm text-gray-700 truncate max-w-[120px] sm:max-w-[150px]">
                      {event.user?.name}
                    </span>
                  </div>
                  <span className="text-xs sm:text-sm text-gray-500 hidden sm:block">
                    {new Date(event.created_at).toLocaleDateString('id-ID', {
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

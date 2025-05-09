import { ChevronLeft, ChevronRight } from 'lucide-react';
import { useRef, useState } from 'react';

import DynamicHeroIcon from '@/components/DynamicHeroIcon';
import { Link } from '@inertiajs/react';
import EventCards from '@/components/EventCard';


export default function EventPage({ dataevent, categories, events }) {
  const [email, setEmail] = useState('');
  const scrollRef = useRef(null);

  const scrollLeft = () => {
    scrollRef.current?.scrollBy({ left: -200, behavior: 'smooth' });
  };

  const scrollRight = () => {
    scrollRef.current?.scrollBy({ left: 200, behavior: 'smooth' });
  };

  const visibleEvents = dataevent.slice(0, 8);

  return (
    <div className="container mx-auto px-4 py-6">
      {/* Event Populer Section */}
      <div className="mb-10">
        <h2 className="text-xl font-bold mb-4">Event Populer</h2>

        <div className="relative">
          {/* Tombol Kiri */}
          <button
            onClick={scrollLeft}
            className="absolute left-0 top-1/2 transform -translate-y-1/2 z-10 bg-white shadow-md p-2 rounded-full hover:bg-purple-100"
          >
            <ChevronLeft className="w-5 h-5 text-purple-600" />
          </button>

          {/* Kategori Scrollable */}
          <div
            ref={scrollRef}
            className="flex overflow-x-auto no-scrollbar px-10 py-4 space-x-20"
          >
            {categories.map((category, index) => (
              <Link
                key={index}
                href={route('events', { category: category.name })}
                className="flex flex-col items-center cursor-pointer group min-w-fit"
              >
                <div className="rounded-full bg-gray-100 p-4 mb-2 group-hover:bg-gray-200 transition-colors">
                  <DynamicHeroIcon
                    iconString={category.icon ?? ""}
                    className="h-6 w-6 text-purple-600 group-hover:text-purple-800 transition-colors"
                  />
                </div>
                <span className="text-xs group-hover:font-medium transition-all text-center whitespace-nowrap">
                  {category.name}
                </span>
              </Link>
            ))}
          </div>

          {/* Tombol Kanan */}
          <button
            onClick={scrollRight}
            className="absolute right-0 top-1/2 transform -translate-y-1/2 z-10 bg-white shadow-md p-2 rounded-full hover:bg-purple-100"
          >
            <ChevronRight className="w-5 h-5 text-purple-600" />
          </button>
        </div>
      </div>

      {/* Event Cards */}
      <div className="grid grid-cols-4 gap-7">
        <EventCards dataevent={dataevent} catevent={events} dataevents={visibleEvents} />
      </div>

      {/* Tombol Jelajah */}
      <div className="w-full flex justify-center mt-10">
        <Link
          href={route('events')}
          className="bg-transparent border-2 border-purple-600 text-purple-600 px-6 py-2 rounded-lg hover:bg-purple-600 hover:text-white transition focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2"
        >
          Jelajah ke Lebih Banyak Event
        </Link>
      </div>
    </div>
  );
};

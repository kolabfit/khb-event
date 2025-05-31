import React, { useRef, useState, useMemo, useEffect } from 'react';
import {
  ChevronLeft,
  ChevronRight,
  Filter,
  SortDesc,
  Clock
} from 'lucide-react';
import DynamicHeroIcon from '@/components/DynamicHeroIcon';
import { Link } from '@inertiajs/react';
import EventCards from '@/components/EventCard';

export default function EventPage({ dataevent, categories = [], events = [] }) {
  const [priceFilter, setPriceFilter] = useState('all'); // 'all', 'free', 'paid'
  const [showRecentOnly, setShowRecentOnly] = useState(false);
  const [sortOrder, setSortOrder] = useState('newest'); // 'newest', 'oldest'
  const [isFilterOpen, setIsFilterOpen] = useState(false);
  const scrollRef = useRef(null);

  // Debug props
  useEffect(() => {
    console.log('EventPage Props:', {
      dataevent,
      categories,
      events
    });
  }, [dataevent, categories, events]);

  const scrollLeft = () => {
    scrollRef.current?.scrollBy({ left: -200, behavior: 'smooth' });
  };

  const scrollRight = () => {
    scrollRef.current?.scrollBy({ left: 200, behavior: 'smooth' });
  };

  const filteredEvents = useMemo(() => {
    // Debug dataevent
    console.log('Filtering events:', dataevent);

    // Ensure we're working with an array
    let filtered = Array.isArray(dataevent) ? [...dataevent] : 
                  (dataevent?.data ? [...dataevent.data] : []);

    console.log('Filtered events before processing:', filtered);

    if (priceFilter === 'free') {
      filtered = filtered.filter(event => event.price === 0 || event.price === '0' || event.price === 'Gratis');
    } else if (priceFilter === 'paid') {
      filtered = filtered.filter(event => event.price > 0 && event.price !== 'Gratis');
    }

    if (showRecentOnly) {
      const oneWeekAgo = new Date();
      oneWeekAgo.setDate(oneWeekAgo.getDate() - 7);
      filtered = filtered.filter(event => new Date(event.date) >= oneWeekAgo);
    }

    filtered.sort((a, b) => {
      const dateA = new Date(a.date);
      const dateB = new Date(b.date);
      return sortOrder === 'newest' ? dateB - dateA : dateA - dateB;
    });

    console.log('Filtered events after processing:', filtered);
    return filtered;
  }, [dataevent, priceFilter, showRecentOnly, sortOrder]);

  return (
    <div className="container mx-auto px-4 py-6">
      {/* Event Populer Section */}
      <div className="mb-10">
        <div className="flex justify-between items-center mb-4">
          <h2 className="text-xl font-bold">Event Populer</h2>
          <div className="flex items-center space-x-2">
            <button
              onClick={() => setIsFilterOpen(!isFilterOpen)}
              className="flex items-center space-x-1 bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1 rounded-md transition"
            >
              <Filter className="w-4 h-4" />
              <span className="text-sm">Filter</span>
            </button>
            <button
              onClick={() => setSortOrder(sortOrder === 'newest' ? 'oldest' : 'newest')}
              className="flex items-center space-x-1 bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1 rounded-md transition"
            >
              <SortDesc className="w-4 h-4" />
              <span className="text-sm">{sortOrder === 'newest' ? 'Terbaru' : 'Terlama'}</span>
            </button>
          </div>
        </div>

        {/* Filter Panel */}
        {isFilterOpen && (
          <div className="bg-white shadow-md rounded-md p-4 mb-4 border border-gray-200">
            <div className="grid grid-cols-2 gap-4">
              <div>
                <h3 className="text-sm font-medium mb-2">Harga</h3>
                <div className="flex space-x-2">
                  <button
                    onClick={() => setPriceFilter('all')}
                    className={`px-3 py-1 text-xs rounded-full ${priceFilter === 'all' ? 'bg-purple-600 text-white' : 'bg-gray-100 text-gray-700'}`}
                  >
                    Semua
                  </button>
                  <button
                    onClick={() => setPriceFilter('free')}
                    className={`px-3 py-1 text-xs rounded-full ${priceFilter === 'free' ? 'bg-purple-600 text-white' : 'bg-gray-100 text-gray-700'}`}
                  >
                    Gratis
                  </button>
                  <button
                    onClick={() => setPriceFilter('paid')}
                    className={`px-3 py-1 text-xs rounded-full ${priceFilter === 'paid' ? 'bg-purple-600 text-white' : 'bg-gray-100 text-gray-700'}`}
                  >
                    Berbayar
                  </button>
                </div>
              </div>

              <div>
                <h3 className="text-sm font-medium mb-2">Waktu</h3>
                <div className="flex items-center">
                  <button
                    onClick={() => setShowRecentOnly(!showRecentOnly)}
                    className={`flex items-center space-x-1 px-3 py-1 text-xs rounded-full ${showRecentOnly ? 'bg-purple-600 text-white' : 'bg-gray-100 text-gray-700'}`}
                  >
                    <Clock className="w-3 h-3" />
                    <span>Baru ditambahkan</span>
                  </button>
                </div>
              </div>
            </div>

            <div className="flex justify-end mt-4">
              <button
                onClick={() => {
                  setPriceFilter('all');
                  setShowRecentOnly(false);
                  setSortOrder('newest');
                }}
                className="text-xs text-purple-600 hover:text-purple-800 mr-4"
              >
                Reset Filter
              </button>
              <button
                onClick={() => setIsFilterOpen(false)}
                className="text-xs bg-purple-600 text-white px-3 py-1 rounded-md hover:bg-purple-700"
              >
                Terapkan
              </button>
            </div>
          </div>
        )}

        {/* Scrollable Categories */}
        <div className="relative">
          <button
            onClick={scrollLeft}
            className="absolute left-0 top-1/2 transform -translate-y-1/2 z-10 bg-white shadow-md p-2 rounded-full hover:bg-purple-100"
          >
            <ChevronLeft className="w-5 h-5 text-purple-600" />
          </button>

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

          <button
            onClick={scrollRight}
            className="absolute right-0 top-1/2 transform -translate-y-1/2 z-10 bg-white shadow-md p-2 rounded-full hover:bg-purple-100"
          >
            <ChevronRight className="w-5 h-5 text-purple-600" />
          </button>
        </div>
      </div>

      {/* Event Cards */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-7">
        <EventCards 
          dataevent={filteredEvents} 
          catevent={events} 
          dataevents={filteredEvents.slice(0, 8)} 
        />
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
}

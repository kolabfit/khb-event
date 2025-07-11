import React, { useRef, useState, useMemo, useEffect } from 'react';
import {
  ChevronLeftIcon,
  ChevronRightIcon,
  Filter,
  SortDesc,
  Clock
} from 'lucide-react';
import DynamicHeroIcon from '@/components/DynamicHeroIcon';
import { Link } from '@inertiajs/react';
import EventCards from '@/components/EventCard';

export default function EventPage({ dataevent, categories = [], events = [], selectedCategory = null }) {
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
      events,
      selectedCategory
    });
  }, [dataevent, categories, events, selectedCategory]);

  const scrollLeft = () => {
    if (scrollRef.current) {
        scrollRef.current.scrollBy({ left: -200, behavior: 'smooth' });
    }
  };

  const scrollRight = () => {
    if (scrollRef.current) {
        scrollRef.current.scrollBy({ left: 200, behavior: 'smooth' });
    }
  };

  const filteredEvents = useMemo(() => {
    // Debug props
    console.log('useMemo states:', { priceFilter, showRecentOnly, sortOrder, selectedCategory });
    console.log('Filtering events:', dataevent);

    // Ensure we're working with an array and clone it
    let filtered = Array.isArray(dataevent?.data) ? [...dataevent.data] : (Array.isArray(dataevent) ? [...dataevent] : []);

    console.log('Filtered events before processing:', filtered.length);

    // Filter out past events (keep only upcoming or today's events)
    const now = new Date();
    const nowOnly = new Date(now.getFullYear(), now.getMonth(), now.getDate()); // Compare only date part

    filtered = filtered.filter(event => {
        if (!event.start_date) return false; // Exclude if start_date is missing
        const eventStartDate = new Date(event.start_date);
        if (isNaN(eventStartDate.getTime())) return false; // Exclude if start_date is invalid

        const eventStartDateOnly = new Date(eventStartDate.getFullYear(), eventStartDate.getMonth(), eventStartDate.getDate());

        return eventStartDateOnly >= nowOnly; // Keep event if start date is today or in the future
    });
     console.log('Filtered events after removing past events:', filtered.length);

    // Price Filtering
    if (priceFilter === 'free') {
      filtered = filtered.filter(event => !event.is_paid);
    } else if (priceFilter === 'paid') {
      filtered = filtered.filter(event => !!event.is_paid);
    }
    console.log('Filtered events after price filter:', filtered.length);

    // Recent Filtering
    if (showRecentOnly) {
      const oneWeekAgo = new Date();
      oneWeekAgo.setDate(oneWeekAgo.getDate() - 7);
      filtered = filtered.filter(event => {
        // Ensure event.created_at is a valid date string before creating Date object
        // Also handle cases where event.created_at might be null or undefined
        if (!event.created_at) return false; // Exclude if created_at is missing
        const eventCreationDate = new Date(event.created_at);
        return !isNaN(eventCreationDate.getTime()) && eventCreationDate >= oneWeekAgo; // Check validity and compare created_at with one week ago
      });
    }
    console.log('Filtered events after recent filter:', filtered.length);


    // Filter by selected category if provided
    if (selectedCategory) {
        filtered = filtered.filter(event =>
            event.category?.name === selectedCategory // Use optional chaining for safer access
        );
    }
    console.log('Filtered events after category filter:', filtered.length);


    // Sorting
    filtered.sort((a, b) => {
      const dateA = new Date(a.start_date);
      const dateB = new Date(b.start_date);
      // Handle invalid dates during sorting - compare valid dates, put invalid at the end
      const timeA = dateA.getTime();
      const timeB = dateB.getTime();

      if (isNaN(timeA) && isNaN(timeB)) return 0; // Both invalid, keep original order
      if (isNaN(timeA)) return 1; // a is invalid, put at end
      if (isNaN(timeB)) return -1; // b is invalid, put at end

      // Sort 'newest' (terdekat) by start_date ascending (timeA - timeB)
      return sortOrder === 'newest' ? timeA - timeB : timeB - timeA;
    });

    console.log('Filtered events after processing:', filtered.length);
    return filtered;
  }, [dataevent, priceFilter, showRecentOnly, sortOrder, selectedCategory]); // Dependency array looks correct

  return (
    <div className="container mx-auto px-4 py-6">
      {/* Event Populer Section */}
      <div className="mb-10">
        <div className="flex justify-between items-center mb-4">
          <h2 className="text-xl font-bold">{selectedCategory ? `${selectedCategory} Events` : 'Event Populer'}</h2>
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

        {/* Kategori Filter - Desktop & Mobile Scrollable */}
        <div className="relative mb-6">
            {/* Tombol Panah Kiri */}
            <button
                onClick={scrollLeft}
                className="absolute left-0 top-1/2 transform -translate-y-1/2 z-10 bg-white shadow-md p-2 rounded-full hover:bg-purple-100"
            >
                <ChevronLeftIcon className="w-5 h-5 text-purple-600" />
            </button>

            <div
                ref={scrollRef}
                className="flex overflow-x-auto no-scrollbar px-10 py-4 space-x-4 md:space-x-16"
            >
                {/* Tombol "All"
                <Link
                    href={route('events')}
                    className={`flex flex-col items-center cursor-pointer group flex-shrink-0 w-[60px] ${
                        !selectedCategory ? 'opacity-100' : 'opacity-70'
                    }`}
                >
                    <div className={`rounded-full p-3 mb-2 transition-colors ${
                        !selectedCategory ? 'bg-purple-600' : 'bg-gray-100'
                    }`}>
                        <DynamicHeroIcon
                            iconString="Squares2X2"
                            className={`h-5 w-5 ${
                                !selectedCategory ? 'text-white' : 'text-purple-600'
                            }`}
                        />
                    </div>
                    <span className="text-xs text-center whitespace-nowrap">All</span>
                </Link> */}

                {categories.map((cat) => (
                    <Link
                        key={cat.id}
                        href={route('events', { category: cat.name })}
                        className={`flex flex-col items-center cursor-pointer group flex-shrink-0 w-[60px] ${
                            selectedCategory === cat.name ? 'opacity-100' : 'opacity-70'
                        }`}
                    >
                        <div className={`rounded-full p-3 mb-2 transition-colors ${
                            selectedCategory === cat.name ? 'bg-purple-600' : 'bg-gray-100'
                        }`}>
                            <DynamicHeroIcon
                                iconString={cat.icon || ''}
                                className={`h-5 w-5 ${
                                    selectedCategory === cat.name ? 'text-white' : 'text-purple-600'
                                }`}
                            />
                        </div>
                        <span className="text-xs text-center whitespace-nowrap">{cat.name}</span>
                    </Link>
                ))}
            </div>

            {/* Tombol Panah Kanan */}
            <button
                onClick={scrollRight}
                className="absolute right-0 top-1/2 transform -translate-y-1/2 z-10 bg-white shadow-md p-2 rounded-full hover:bg-purple-100"
            >
                <ChevronRightIcon className="w-5 h-5 text-purple-600" />
            </button>
        </div>
      </div>

      {/* Event Cards */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-7">
        <EventCards 
          dataevent={filteredEvents}
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

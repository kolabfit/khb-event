import React, { useRef, useState, useMemo } from 'react';
import { Head, Link } from '@inertiajs/react';
import Navbar from '@/components/Navbar';
import Footer from '@/Components/Footer';
import DynamicHeroIcon from '@/components/DynamicHeroIcon';
import EventCards from '@/components/EventCard';
import { ChevronLeftIcon, ChevronRightIcon } from '@heroicons/react/24/outline';
import { SortDesc, Filter, Clock } from 'lucide-react';

export default function EventsPage({ auth, events, categories, selectedCategory }) {
    console.log('EventsPage - selectedCategory:', selectedCategory);
    console.log('EventsPage - first event start_date:', events && events.length > 0 ? events[0].start_date : 'No events');
    console.log('EventsPage - first event category:', events && events.length > 0 ? events[0].category : 'No events');
    console.log('EventsPage - first event object:', events && events.length > 0 ? events[0] : 'No events');

    const scrollRef = useRef(null);
    const [sortOrder, setSortOrder] = useState('newest'); // 'newest', 'oldest'

    // *** Added state for filter panel ***
    const [isFilterOpen, setIsFilterOpen] = useState(false);
    const [priceFilter, setPriceFilter] = useState('all'); // 'all', 'free', 'paid'
    const [showRecentOnly, setShowRecentOnly] = useState(false);

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

    // *** Modified useMemo to split into upcoming and past events and apply filters/sorting ***
    const { upcomingEvents, pastEvents } = useMemo(() => {
        let filteredEvents = Array.isArray(events) ? [...events] : [];

        // Apply category filter first
        if (selectedCategory) {
             filteredEvents = filteredEvents.filter(event =>
                // Check if the event has categories and if any category name matches the selectedCategory
                event.categories && // Ensure event.categories is not null/undefined
                Array.isArray(event.categories) && // Ensure event.categories is an array
                event.categories.some(category => category.name === selectedCategory) // Check if any category in the array matches
             );
         }

        // Apply Price Filtering
         if (priceFilter === 'free') {
             filteredEvents = filteredEvents.filter(event => !event.is_paid);
         } else if (priceFilter === 'paid') {
             filteredEvents = filteredEvents.filter(event => !!event.is_paid);
         }

         // Apply Recent Filtering
         if (showRecentOnly) {
             const oneWeekAgo = new Date();
             oneWeekAgo.setDate(oneWeekAgo.getDate() - 7);
             filteredEvents = filteredEvents.filter(event => {
                 if (!event.start_date) return false;
                 const eventDate = new Date(event.start_date);
                 return !isNaN(eventDate.getTime()) && eventDate >= oneWeekAgo;
             });
         }

        const now = new Date();
        const upcoming = [];
        const past = [];

        filteredEvents.forEach(event => {
            if (!event.start_date) {
                 console.log('Event filtered out during split: Missing start_date', event);
                return; // Skip events without a start_date
            }

            const eventStartDate = new Date(event.start_date);
            if (isNaN(eventStartDate.getTime())) {
                  console.log('Event filtered out during split: Invalid start_date format', event.start_date, event);
                return; // Skip events with invalid date
            }

            // Use getHours, getMinutes, getSeconds, getMilliseconds to set time to 00:00:00 for date comparison only
            const eventStartDateOnly = new Date(eventStartDate.getFullYear(), eventStartDate.getMonth(), eventStartDate.getDate());
            const nowOnly = new Date(now.getFullYear(), now.getMonth(), now.getDate());

            if (eventStartDateOnly >= nowOnly) {
                console.log('Adding to upcoming (date comparison):', event.title, event.start_date, eventStartDateOnly, nowOnly);
                upcoming.push(event);
            } else {
                console.log('Adding to past (date comparison):', event.title, event.start_date, eventStartDateOnly, nowOnly);
                past.push(event);
            }
        });

        // Sort upcoming events by start_date (newest = terdekat)
        upcoming.sort((a, b) => {
            const dateA = new Date(a.start_date);
            const dateB = new Date(b.start_date);
            // Handle invalid dates during sorting - put invalid at the end
            const timeA = dateA.getTime();
            const timeB = dateB.getTime();

            if (isNaN(timeA) && isNaN(timeB)) return 0;
            if (isNaN(timeA)) return 1;
            if (isNaN(timeB)) return -1;

            return sortOrder === 'newest' ? timeA - timeB : timeB - timeA; // sortOrder applies to upcoming
        });

        // Sort past events by start_date (most recent first)
        past.sort((a, b) => {
             const dateA = new Date(a.start_date);
             const dateB = new Date(b.start_date);
              // Handle invalid dates during sorting - put invalid at the end
             const timeA = dateA.getTime();
             const timeB = dateB.getTime();

            if (isNaN(timeA) && isNaN(timeB)) return 0;
            if (isNaN(timeA)) return 1;
            if (isNaN(timeB)) return -1;

             return timeB - timeA; // Sort past events by most recent date
         });

        return { upcomingEvents: upcoming, pastEvents: past };
    }, [events, selectedCategory, priceFilter, showRecentOnly, sortOrder]); // Added dependencies

    return (
        <>
            <Head title="Events" />

            {/* Kirim auth ke Navbar */}
            <Navbar auth={auth} />

            <main className="container mx-auto px-4 py-6 mb-10">
                {/* Judul Halaman & Filter/Sort Buttons */}
                <div className="flex justify-between items-center mb-4">
                    <h1 className="text-xl sm:text-2xl font-bold text-center flex-grow">
                        {selectedCategory ? `${selectedCategory} Events` : 'All Events'}
                    </h1>
                     
                </div>

                

                {/* Pilihan Kategori - Desktop */}
                <div className="hidden md:flex justify-center flex-wrap gap-2 sm:gap-3 mb-6 sm:mb-10 px-2">
                    {/* Tombol "All" */}
                    <Link
                        href={route('events')}
                        className={`flex items-center space-x-1 px-3 py-1.5 rounded-full text-sm font-medium transition
                            ${!selectedCategory
                                ? 'bg-purple-600 text-white'
                                : 'bg-gray-100 text-gray-800 hover:bg-gray-200'}
                        `}
                    >
                        <DynamicHeroIcon
                            iconString="Squares2X2Icon"
                            className="w-4 h-4 sm:w-5 sm:h-5"
                        />
                        <span>All</span>
                    </Link>

                    {categories.map(cat => (
                        <Link
                            key={cat.id}
                            href={route('events', { category: cat.name })}
                            className={`flex items-center space-x-1 px-3 py-1.5 rounded-full text-sm font-medium transition
                                ${selectedCategory === cat.name
                                    ? 'bg-purple-600 text-white'
                                    : 'bg-gray-100 text-gray-800 hover:bg-gray-200'}
                            `}
                        >
                            <DynamicHeroIcon
                                iconString={cat.icon || ''}
                                className="w-4 h-4 sm:w-5 sm:h-5"
                            />
                            <span>{cat.name}</span>
                        </Link>
                    ))}
                </div>

                {/* Pilihan Kategori - Mobile Scrollable */}
                <div className="md:hidden relative mb-6">
                    <button
                        onClick={scrollLeft}
                        className="absolute left-0 top-1/2 transform -translate-y-1/2 z-10 bg-white shadow-md p-2 rounded-full hover:bg-purple-100"
                    >
                        <ChevronLeftIcon className="w-5 h-5 text-purple-600" />
                    </button>

                    <div
                        ref={scrollRef}
                        className="flex overflow-x-auto no-scrollbar px-10 py-4 space-x-4"
                    >
                        {/* All Category */}
                        <Link
                            href={route('events')}
                            className={`flex items-center justify-center w-14 h-14 rounded-full text-sm font-medium transition-colors flex-shrink-0 ${
                                !selectedCategory ? 'bg-purple-600 text-white' : 'bg-gray-100 text-gray-800 hover:bg-gray-200'
                            }`}
                        >
                            <span className="text-xs text-center whitespace-nowrap">All</span>
                        </Link>

                        {categories.map((cat) => (
                            <Link
                                key={cat.id}
                                href={route('events', { category: cat.name })}
                                className={`flex flex-col items-center cursor-pointer group min-w-[60px] ${
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

                    <button
                        onClick={scrollRight}
                        className="absolute right-0 top-1/2 transform -translate-y-1/2 z-10 bg-white shadow-md p-2 rounded-full hover:bg-purple-100"
                    >
                        <ChevronRightIcon className="w-5 h-5 text-purple-600" />
                    </button>
                </div>

                {/* Daftar Event - Responsive Grid */}
                {/* Upcoming Events Section */}
                {upcomingEvents.length > 0 && (
                    <div className="mb-10">
                        {/* Title and Filter/Sort Buttons */}
                        <div className="flex justify-between items-center mb-4">
                            <h2 className="text-xl font-bold">Event yang Akan Datang</h2>
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
                                     <span className="text-sm">{sortOrder === 'newest' ? 'Terdekat' : 'Terjauh'}</span>
                                 </button>
                            </div>
                        </div>

                        {/* Filter Panel (Moved Here) */}
                        {isFilterOpen && (
                            <div className="bg-white shadow-md rounded-md p-4 mb-4 border border-gray-200">
                                <div className="grid grid-cols-2 gap-4">
                                    <div>
                                        <h3 className="text-sm font-medium mb-2">Harga</h3>
                                        <div className="flex space-x-2">
                                            <button
                                                onClick={() => {
                                                    setPriceFilter('all');
                                                    console.log('Price filter set to all');
                                                }}
                                                className={`px-3 py-1 text-xs rounded-full ${priceFilter === 'all' ? 'bg-purple-600 text-white' : 'bg-gray-100 text-gray-700'}`}
                                            >
                                                Semua
                                            </button>
                                            <button
                                                onClick={() => {
                                                    setPriceFilter('free');
                                                    console.log('Price filter set to free');
                                                }}
                                                className={`px-3 py-1 text-xs rounded-full ${priceFilter === 'free' ? 'bg-purple-600 text-white' : 'bg-gray-100 text-gray-700'}`}
                                            >
                                                Gratis
                                            </button>
                                            <button
                                                onClick={() => {
                                                    setPriceFilter('paid');
                                                    console.log('Price filter set to paid');
                                                }}
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
                                                onClick={() => {
                                                    setShowRecentOnly(!showRecentOnly);
                                                    console.log('Show recent only toggled to:', !showRecentOnly);
                                                }}
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
                                            setSortOrder('newest'); // Reset sort as well
                                            console.log('Filter reset');
                                        }}
                                        className="text-xs text-purple-600 hover:text-purple-800 mr-4"
                                    >
                                        Reset Filter
                                    </button>
                                    <button
                                        onClick={() => {
                                            setIsFilterOpen(false);
                                            console.log('Filter panel closed');
                                        }}
                                        className="text-xs bg-purple-600 text-white px-3 py-1 rounded-md hover:bg-purple-700"
                                    >
                                        Terapkan
                                    </button>
                                </div>
                            </div>
                        )}

                        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
                            <EventCards dataevent={upcomingEvents} />
                        </div>
                    </div>
                )}

                {/* Past Events Section */}
                {pastEvents.length > 0 && (
                     <div className="mb-10">
                         <h2 className="text-xl font-bold mb-4">Event yang Sudah Selesai</h2>
                         <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
                             <EventCards dataevent={pastEvents} />
                         </div>
                     </div>
                 )}

                 {upcomingEvents.length === 0 && pastEvents.length === 0 && (
                     <div className="col-span-full text-center py-8 text-gray-500">
                         Tidak ada event yang tersedia sesuai filter.
                     </div>
                 )}

            </main>

            <Footer />
        </>
    );
}

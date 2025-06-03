import React, { useRef } from 'react';
import { Head, Link } from '@inertiajs/react';
import Navbar from '@/components/Navbar';
import Footer from '@/Components/Footer';
import DynamicHeroIcon from '@/components/DynamicHeroIcon';
import EventCards from '@/components/EventCard';
import { ChevronLeftIcon, ChevronRightIcon } from '@heroicons/react/24/outline';
import categories from '@/data/categories'; // Import categories data

export default function EventsPage({ auth, events, categories, selectedCategory }) {
    const scrollRef = useRef(null);

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

    return (
        <>
            <Head title="Events" />

            {/* Kirim auth ke Navbar */}
            <Navbar auth={auth} />

            <main className="container mx-auto px-4 py-6 mb-10">
                {/* Judul Halaman */}
                <h1 className="text-xl sm:text-2xl font-bold mb-4 sm:mb-6 text-center">
                    {selectedCategory ? `${selectedCategory} Events` : 'All Events'}
                </h1>

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
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
                    <EventCards dataevent={events} catevents={events} />
                </div>
            </main>

            <Footer />
        </>
    );
}

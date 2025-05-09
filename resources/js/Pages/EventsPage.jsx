import React from 'react';
import { Head, Link } from '@inertiajs/react';
import Navbar from '@/components/Navbar';
import Footer from '@/components/Footer';
import DynamicHeroIcon from '@/components/DynamicHeroIcon';
import EventCards from '@/components/EventCard';

export default function EventsPage({ auth, events, categories, selectedCategory }) {
    return (
        <>
            <Head title="Events" />

            {/* Kirim auth ke Navbar */}
            <Navbar auth={auth} />

            <main className="container mx-auto px-4 py-6 mb-10">
                {/* Judul Halaman */}
                <h1 className="text-2xl font-bold mb-6 justify-center text-center">
                    {selectedCategory ? `${selectedCategory} Events` : 'All Events'}
                </h1>

                {/* Pilihan Kategori */}
                <div className="flex justify-center flex-wrap gap-3 mb-10">
                    {/* Tombol “All” */}
                    <Link
                        href={route('events')}
                        className={`flex items-center space-x-1 px-3 py-1 rounded-full text-sm font-medium transition
      ${!selectedCategory
                                ? 'bg-purple-600 text-white'
                                : 'bg-gray-100 text-gray-800 hover:bg-gray-200'}
    `}
                    >
                        <span>All</span>
                    </Link>

                    {categories.map(cat => (
                        <Link
                            key={cat.id}
                            href={route('events', { category: cat.name })}
                            className={`flex items-center space-x-1 px-3 py-1 rounded-full text-sm font-medium transition
        ${selectedCategory === cat.name
                                    ? 'bg-purple-600 text-white'
                                    : 'bg-gray-100 text-gray-800 hover:bg-gray-200'}
      `}
                        >
                            <DynamicHeroIcon
                                iconString={cat.icon || ''}
                                className="w-5 h-5"
                            />
                            <span>{cat.name}</span>
                        </Link>
                    ))}
                </div>

                {/* Daftar Event (4 kolom) */}
                <div className="grid grid-cols-4 gap-6">
                    <EventCards dataevent={events} catevents={events} />
                </div>
            </main>

            <Footer />
        </>
    );
}

import { Link, Head } from '@inertiajs/react';
import Navbar from '@/components/Navbar';

export default function Welcome({ auth, events, filters }) {
    return (
        <>
            <Head title="Welcome" />
            <div className="min-h-screen bg-gray-100">
                <Navbar auth={auth} />

                <div className="py-12">
                    <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                        {/* Search Results Count */}
                        {filters.search && (
                            <div className="mb-4 text-gray-600">
                                Showing results for "{filters.search}" ({events.total} events found)
                            </div>
                        )}

                        {/* Events Grid */}
                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            {events.data.map((event) => (
                                <div key={event.id} className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                                    <div className="p-6">
                                        <h2 className="text-xl font-semibold mb-2">{event.title}</h2>
                                        <p className="text-gray-600 mb-4">{event.description}</p>
                                        <div className="flex justify-between items-center">
                                            <span className="text-sm text-gray-500">{event.location}</span>
                                            <Link
                                                href={`/events/${event.id}`}
                                                className="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 focus:bg-purple-700 active:bg-purple-900 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                            >
                                                View Details
                                            </Link>
                                        </div>
                                    </div>
                                </div>
                            ))}
                        </div>

                        {/* Pagination */}
                        {events.data.length > 0 ? (
                            <div className="mt-6">
                                {/* Add your pagination component here */}
                            </div>
                        ) : (
                            <div className="text-center py-12">
                                <p className="text-gray-500 text-lg">
                                    {filters.search 
                                        ? `No events found matching "${filters.search}"`
                                        : 'No events available at the moment'}
                                </p>
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </>
    );
}

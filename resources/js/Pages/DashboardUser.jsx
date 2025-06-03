import { Head } from '@inertiajs/react';
import Navbar from '@/components/Navbar';
import Footer from '@/Components/Footer';
import HeroSection from '@/Components/HeroSection';
import EventPage from '@/Components/EventPage';
import GuestLayout from '@/Layouts/GuestLayout';

export default function DashboardUser({ auth, dataevent, category }) {
    return (
        <>
            <Head title="Dashboard" />
            <div className="min-h-screen bg-gray-50">
                <Navbar auth={auth} />
                <HeroSection />
                <EventPage 
                    dataevent={dataevent} 
                    categories={category} 
                    events={dataevent?.data || []} 
                />
                <Footer />
            </div>
        </>
    );
}

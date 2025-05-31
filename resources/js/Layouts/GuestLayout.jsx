import ApplicationLogo from '@/components/ApplicationLogo';
import { Link } from '@inertiajs/react';

export default function Guest({ children }) {
    return (
        <div className="min-h-screen bg-gray-100 flex flex-col items-center justify-center">
            <div className="w-full sm:max-w-md px-6 py-4">
                <div className="flex justify-center mb-6">
                    <Link href="/">
                        <img src="/logo/khb.png" alt="KHB Logo" className="h-16" />
                    </Link>
                </div>
                <div className="bg-white shadow-md overflow-hidden sm:rounded-lg p-6">
                    {children}
                </div>
            </div>
        </div>
    );
}

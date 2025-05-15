import React, { useState, useRef, useEffect } from 'react';
import { Link } from '@inertiajs/inertia-react';

export default function Navbar({ auth }) {
  const user = auth?.user;

  // State & ref for the avatar dropdown
  const [open, setOpen] = useState(false);
  const dropdownRef = useRef(null);

  // Close dropdown when clicking outside
  useEffect(() => {
    const onClickOutside = (e) => {
      if (dropdownRef.current && !dropdownRef.current.contains(e.target)) {
        setOpen(false);
      }
    };
    document.addEventListener('mousedown', onClickOutside);
    return () => document.removeEventListener('mousedown', onClickOutside);
  }, []);

  return (
    <nav className="bg-white shadow-sm">
      <div className="container mx-auto px-4">
        <div className="flex items-center justify-between py-3">
          {/* Logo */}
          <Link href={route('dashboard')} className="flex items-center space-x-2">
            <div className="flex items-center">
              {/* …your logo svg… */}
              {/* <span className="text-xl font-bold text-indigo-900">KHB EVENT</span> */}
              <img src="/logo/khb.png" alt="" style={{ width: '100px', height: '40px' }}/>
            </div>
          </Link>


          {/* Search Bar */}
          <div className="flex items-center px-3 py-1 mx-4 bg-gray-100 rounded-md flex-grow max-w-md">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              className="w-5 h-5 text-gray-400"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                strokeLinecap="round"
                strokeLinejoin="round"
                strokeWidth={2}
                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
              />
            </svg>
            <input
              type="text"
              placeholder="Cari event"
              className="w-full px-2 py-1 ml-2 bg-transparent text-gray-600 outline-none border-0 focus:ring-0"
            />
          </div>


          {/* User Avatar / Login */}
          <div className="flex items-center">
            {!user ? (
              <Link
                href="/login"
                className="px-5 py-2 font-medium text-black bg-green-300 rounded-full hover:bg-green-400 transition"
              >
                Login
              </Link>
            ) : (
              <div className="relative" ref={dropdownRef}>
                <button
                  onClick={() => setOpen((o) => !o)}
                  className="focus:outline-none"
                >
                  <img
                    src={user.avatar}
                    alt="Avatar"
                    className="w-8 h-8 rounded-full object-cover"
                  />
                </button>
                {open && (
                  <div className="absolute right-0 mt-2 w-40 bg-white shadow-lg rounded-md overflow-hidden z-10">
                    <Link
                      href={route('profile.edit')}
                      className="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                    >
                      Profile
                    </Link>
                    <Link
                      href="/logout"
                      method="post"
                      as="button"
                      className="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 text-red-500"
                    >
                      Logout
                    </Link>
                  </div>
                )}
              </div>
            )}
          </div>
        </div>
      </div>
    </nav>
  );
}

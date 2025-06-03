import React, { useState, useRef, useEffect } from 'react';
import { Link } from '@inertiajs/react';
import { NotepadText, X, Menu, Search } from 'lucide-react';

export default function Navbar({ auth }) {
  const user = auth?.user;

  // Log user.avatar to the console for debugging
  useEffect(() => {
    if (user) {
      console.log('User object in Navbar:', user);
      console.log('user.avatar value:', user.avatar);
    }
  }, [user]);

  // State & ref for the avatar dropdown
  const [open, setOpen] = useState(false);
  const dropdownRef = useRef(null);

  // State for mobile menu
  const [isMobileMenuOpen, setIsMobileMenuOpen] = useState(false);

  // State for the request event modal
  const [showRequestModal, setShowRequestModal] = useState(false);
  const [requestForm, setRequestForm] = useState({
    nama: '',
    penanggungjawab: '',
    kontak: '',
    alamat: '',
    namakegiatan: '',
    deskripsi: '',
    tanggal: ''
  });
  const modalRef = useRef(null);

  // Handle form input changes
  const handleInputChange = (e) => {
    const { name, value } = e.target;
    setRequestForm(prev => ({
      ...prev,
      [name]: value
    }));
  };

  // Handle form submission
  const handleSubmit = (e) => {
    e.preventDefault();
    console.log('Form submitted:', requestForm);
    alert('Request event berhasil dikirim!');
    setShowRequestModal(false);
    setRequestForm({
      nama: '',
      penanggungjawab: '',
      kontak: '',
      alamat: '',
      namakegiatan: '',
      deskripsi: '',
      tanggal: ''
    });
  };

  // Close dropdown when clicking outside
  useEffect(() => {
    const onClickOutside = (e) => {
      if (dropdownRef.current && !dropdownRef.current.contains(e.target)) {
        setOpen(false);
      }
      if (modalRef.current && !modalRef.current.contains(e.target) && showRequestModal) {
        setShowRequestModal(false);
      }
    };
    document.addEventListener('mousedown', onClickOutside);
    return () => document.removeEventListener('mousedown', onClickOutside);
  }, [showRequestModal]);

  // Function to generate initials
  const generateInitials = (name) => {
    if (!name) return '';
    const parts = name.split(' ');
    if (parts.length === 1) {
      return parts[0].charAt(0).toUpperCase();
    } else {
      return (parts[0].charAt(0) + parts[parts.length - 1].charAt(0)).toUpperCase();
    }
  };

  // Check if routes exist before using them
  const dashboardRoute = route().has('dashboard') ? route('dashboard') : '/';
  const historyRoute = route().has('history') ? route('history') : '/event-history';
  const profileEditRoute = route('profile.edit');

  // Helper to check if avatar is a potentially valid URL string
  const isAvatarUrl = (avatar) => {
    // Check if it's a non-empty string, not the string "null" (case-insensitive),
    // not the string "/storage/", and looks like a valid path or URL
    return typeof avatar === 'string' && 
           avatar.trim() !== '' && 
           avatar.toLowerCase() !== 'null' && // Check for the string "null"
           avatar !== '/storage/' && // Explicitly exclude the problematic "/storage/" string
           (avatar.startsWith('http') || avatar.startsWith('/') || avatar.includes('.')); // Looks like a valid path
  };

  return (
    <nav className="bg-white shadow-sm">
      <div className="container mx-auto px-4">
        <div className="flex items-center justify-between py-3">
          {/* Logo and Request Event */}
          <div className="flex items-center space-x-4 sm:space-x-6">
            <Link href={dashboardRoute} className="flex items-center">
              <img src="/logo/khb.png" alt="KHB Logo" className="w-20 sm:w-24 h-auto"/>
            </Link>
            
            {/* Request Event Button - Hidden on mobile */}
            <button 
              onClick={() => setShowRequestModal(true)}
              className="hidden sm:block text-gray-700 font-medium hover:text-purple-600 transition duration-200"
            >
              Request Event
            </button>
          </div>

          {/* Search Bar - Hidden on mobile */}
          <div className="hidden sm:flex items-center px-3 py-1 mx-4 bg-gray-100 rounded-md flex-grow max-w-md">
            <Search className="w-5 h-5 text-gray-400" />
            <input
              type="text"
              placeholder="Cari event"
              className="w-full px-2 py-1 ml-2 bg-transparent text-gray-600 outline-none border-0 focus:ring-0"
            />
          </div>

          {/* Mobile Menu Button */}
          <button
            onClick={() => setIsMobileMenuOpen(!isMobileMenuOpen)}
            className="sm:hidden p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100 focus:outline-none"
          >
            <Menu className="w-6 h-6" />
          </button>

          {/* Desktop Navigation */}
          <div className="hidden sm:flex items-center space-x-5">
            {/* History Icon */}
            <Link 
              href={historyRoute}
              className="text-gray-600 hover:text-purple-600 transition duration-200"
            >
              <NotepadText className="w-6 h-6" />
            </Link>

            {/* User Avatar / Login */}
            {!user ? (
              <div className="flex items-center space-x-3">
                <Link
                  href="/register"
                  className="px-4 py-2 text-sm font-medium text-black rounded-full hover:bg-purple-400 transition"
                >
                  Register
                </Link>
                <Link
                  href="/login"
                  className="px-4 py-2 text-sm font-medium text-black bg-green-300 rounded-full hover:bg-green-400 transition"
                >
                  Login
                </Link>
              </div>
            ) : (
              <div className="relative" ref={dropdownRef}>
                <button
                  onClick={() => setOpen((o) => !o)}
                  className="focus:outline-none flex items-center space-x-2"
                >
                  {/* Render img only if user.avatar looks like a valid URL string */}
                  {isAvatarUrl(user.avatar) ? (
                    <img
                      src={user.avatar}
                      alt="Avatar"
                      className="w-8 h-8 rounded-full object-cover"
                    />
                  ) : (
                    <div className="w-8 h-8 rounded-full bg-purple-500 text-white flex items-center justify-center text-sm font-medium">
                      {generateInitials(user.name)}
                    </div>
                  )}
                </button>
                {open && (
                  <div className="absolute right-0 mt-2 w-48 bg-white shadow-lg rounded-md overflow-hidden z-10">
                    {/* Display username at the top of the dropdown */}
                    {/* <div className="block px-4 py-2 text-sm text-gray-700">{user.name}</div> */}
                    <Link
                      href={profileEditRoute}
                      className="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 inline-flex items-center"
                    >
                      Profile
                    </Link>
                    <Link
                      href="/logout"
                      method="post"
                      as="button"
                      className="block w-full text-left px-4 py-2 text-sm text-red-500 hover:bg-gray-100 inline-flex items-center"
                      onClick={() => setOpen(false)}
                    >
                      Logout
                    </Link>
                  </div>
                )}
              </div>
            )}
          </div>
        </div>

        {/* Mobile Menu */}
        {isMobileMenuOpen && (
          <div className="sm:hidden py-4 border-t">
            {/* Mobile Search */}
            <div className="flex items-center px-3 py-2 mb-4 bg-gray-100 rounded-md">
              <Search className="w-5 h-5 text-gray-400" />
              <input
                type="text"
                placeholder="Cari event"
                className="w-full px-2 py-1 ml-2 bg-transparent text-gray-600 outline-none border-0 focus:ring-0"
              />
            </div>

            {/* Mobile Navigation Links */}
            <div className="space-y-3 px-4">
              <button 
                onClick={() => {
                  setShowRequestModal(true);
                  setIsMobileMenuOpen(false);
                }}
                className="w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-md"
              >
                Request Event
              </button>
              
              <Link 
                href={historyRoute}
                className="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-md"
                onClick={() => setIsMobileMenuOpen(false)}
              >
                History
              </Link>

              {!user ? (
                <div className="space-y-2">
                  <Link
                    href="/register"
                    className="block w-full px-4 py-2 text-center text-sm font-medium text-black rounded-full hover:bg-purple-400 transition"
                    onClick={() => setIsMobileMenuOpen(false)}
                  >
                    Register
                  </Link>
                  <Link
                    href="/login"
                    className="block w-full px-4 py-2 text-center text-sm font-medium text-black bg-green-300 rounded-full hover:bg-green-400 transition"
                    onClick={() => setIsMobileMenuOpen(false)}
                  >
                    Login
                  </Link>
                </div>
              ) : (
                <div className="space-y-2">
                  <div className="flex items-center space-x-2 justify-center">
                      {/* Render img only if user.avatar looks like a valid URL string */}
                      {isAvatarUrl(user.avatar) ? (
                          <img
                              src={user.avatar}
                              alt="Avatar"
                              className="w-8 h-8 rounded-full object-cover"
                          />
                      ) : (
                          <div className="w-8 h-8 rounded-full bg-purple-500 text-white flex items-center justify-center text-sm font-medium">
                              {generateInitials(user.name)}
                          </div>
                      )}
                      <span className="font-medium text-gray-700">{user.name}</span>
                  </div>
                  <Link
                    href={profileEditRoute}
                    className="block w-full px-4 py-2 text-center text-sm text-gray-700 hover:bg-gray-100 rounded-md"
                    onClick={() => setIsMobileMenuOpen(false)}
                  >
                    Profile
                  </Link>
                  <Link
                    href="/logout"
                    method="post"
                    as="button"
                    className="block w-full px-4 py-2 text-center text-sm text-white bg-red-500 hover:bg-red-600 rounded-md font-medium transition-colors duration-200"
                    onClick={() => setIsMobileMenuOpen(false)}
                  >
                    Logout
                  </Link>
                </div>
              )}
            </div>
          </div>
        )}
      </div>

      {/* Request Event Modal */}
      {showRequestModal && (
        <div className="fixed inset-0 z-50 overflow-auto bg-black bg-opacity-50 flex items-center justify-center p-4">
          <div
            ref={modalRef}
            className="bg-white rounded-lg shadow-xl p-2 sm:p-3 md:p-4 w-72 mx-auto sm:w-full sm:max-w-sm md:max-w-md"
            onClick={(e) => e.stopPropagation()} // Prevent modal closing when clicking inside
          >
            <div className="flex justify-between items-center mb-4">
              <h2 className="text-lg font-bold">Request Event Baru</h2>
              <button onClick={() => setShowRequestModal(false)} className="text-gray-500 hover:text-gray-700">
                <X className="w-5 h-5" />
              </button>
            </div>
            <form onSubmit={handleSubmit} className="space-y-4">
              <div>
                <label htmlFor="nama" className="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                <input
                  type="text"
                  name="nama"
                  id="nama"
                  value={requestForm.nama}
                  onChange={handleInputChange}
                  required
                  className="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm"
                />
              </div>
              <div>
                <label htmlFor="penanggungjawab" className="block text-sm font-medium text-gray-700">Penanggung Jawab</label>
                <input
                  type="text"
                  name="penanggungjawab"
                  id="penanggungjawab"
                  value={requestForm.penanggungjawab}
                  onChange={handleInputChange}
                  required
                  className="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm"
                />
              </div>
              <div>
                <label htmlFor="kontak" className="block text-sm font-medium text-gray-700">Kontak (Email/Telepon)</label>
                <input
                  type="text"
                  name="kontak"
                  id="kontak"
                  value={requestForm.kontak}
                  onChange={handleInputChange}
                  required
                  className="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm"
                />
              </div>
              <div>
                <label htmlFor="alamat" className="block text-sm font-medium text-gray-700">Alamat Lengkap</label>
                <textarea
                  name="alamat"
                  id="alamat"
                  rows="3"
                  value={requestForm.alamat}
                  onChange={handleInputChange}
                  required
                  className="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm"
                ></textarea>
              </div>
              <div>
                <label htmlFor="namakegiatan" className="block text-sm font-medium text-gray-700">Nama Kegiatan/Event</label>
                <input
                  type="text"
                  name="namakegiatan"
                  id="namakegiatan"
                  value={requestForm.namakegiatan}
                  onChange={handleInputChange}
                  required
                  className="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm"
                />
              </div>
              <div>
                <label htmlFor="deskripsi" className="block text-sm font-medium text-gray-700">Deskripsi Kegiatan/Event</label>
                <textarea
                  name="deskripsi"
                  id="deskripsi"
                  rows="4"
                  value={requestForm.deskripsi}
                  onChange={handleInputChange}
                  required
                  className="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm"
                ></textarea>
              </div>
              <div>
                <label htmlFor="tanggal" className="block text-sm font-medium text-gray-700">Tanggal Pelaksanaan</label>
                <input
                  type="date"
                  name="tanggal"
                  id="tanggal"
                  value={requestForm.tanggal}
                  onChange={handleInputChange}
                  required
                  className="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm"
                />
              </div>
              <div className="flex justify-end">
                <button
                  type="submit"
                  className="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500"
                >
                  Kirim Request
                </button>
              </div>
            </form>
          </div>
        </div>
      )}
    </nav>
  );
}
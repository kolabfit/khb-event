import React, { useState, useRef, useEffect } from 'react';
import { Link } from '@inertiajs/react';
import { NotepadText, X } from 'lucide-react';

export default function Navbar({ auth }) {
  const user = auth?.user;

  // State & ref for the avatar dropdown
  const [open, setOpen] = useState(false);
  const dropdownRef = useRef(null);

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

  // Check if routes exist before using them
  const dashboardRoute = route().has('dashboard') ? route('dashboard') : '/';
  const historyRoute = route().has('history') ? route('history') : '/event-history';
  const profileEditRoute = route('profile.edit');

  return (
    <nav className="bg-white shadow-sm">
      <div className="container mx-auto px-4">
        <div className="flex items-center justify-between py-3">
          {/* Logo and Request Event */}
          <div className="flex items-center space-x-6">
            <Link href={dashboardRoute} className="flex items-center">
              <img src="/logo/khb.png" alt="KHB Logo" style={{ width: '100px', height: '40px' }}/>
            </Link>
            
            {/* Request Event Button */}
            <button 
              onClick={() => setShowRequestModal(true)}
              className="text-gray-700 font-medium hover:text-purple-600 transition duration-200"
            >
              Request Event
            </button>
          </div>

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

          {/* History Icon and User Avatar / Login */}
          <div className="flex items-center space-x-5">
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
                  className="px-5 py-2 font-small text-black rounded-full hover:bg-purple-400 transition"
                >
                  Register
                </Link>
                <Link
                  href="/login"
                  className="px-5 py-2 font-small text-black bg-green-300 rounded-full hover:bg-green-400 transition"
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
                  <span className="font-small text-gray-500">{user.name}</span>
                  <img
                    src={user.avatar || '/images/default-avatar.png'}
                    alt="Avatar"
                    className="w-8 h-8 rounded-full object-cover"
                  />
                </button>
                {open && (
                  <div className="absolute right-0 mt-2 w-40 bg-white shadow-lg rounded-md overflow-hidden z-10">
                    <Link
                      href={profileEditRoute}
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

      {/* Request Event Modal */}
      {showRequestModal && (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
          <div 
            ref={modalRef}
            className="bg-white rounded-lg shadow-lg p-6 w-full max-w-md"
          >
            <div className="flex justify-between items-center mb-4">
              <h2 className="text-xl font-bold text-gray-800">Request Event</h2>
              <button 
                onClick={() => setShowRequestModal(false)}
                className="text-gray-500 hover:text-gray-700"
              >
                <X className="w-5 h-5" />
              </button>
            </div>
            
            <form onSubmit={handleSubmit}>
              <div className="space-y-4">
                <div>
                  <label htmlFor="nama" className="block text-sm font-medium text-gray-700 mb-1">
                    Nama
                  </label>
                  <input
                    type="text"
                    id="nama"
                    name="nama"
                    value={requestForm.nama}
                    onChange={handleInputChange}
                    className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-purple-500"
                    required
                  />
                </div>
                
                <div>
                  <label htmlFor="penanggungjawab" className="block text-sm font-medium text-gray-700 mb-1">
                    Penanggung Jawab
                  </label>
                  <input
                    type="text"
                    id="penanggungjawab"
                    name="penanggungjawab"
                    value={requestForm.penanggungjawab}
                    onChange={handleInputChange}
                    className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-purple-500"
                    required
                  />
                </div>
                
                <div>
                  <label htmlFor="kontak" className="block text-sm font-medium text-gray-700 mb-1">
                    Kontak Penanggung Jawab
                  </label>
                  <input
                    type="text"
                    id="kontak"
                    name="kontak"
                    value={requestForm.kontak}
                    onChange={handleInputChange}
                    className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-purple-500"
                    required
                  />
                </div>
                
                <div>
                  <label htmlFor="alamat" className="block text-sm font-medium text-gray-700 mb-1">
                    Alamat
                  </label>
                  <input
                    type="text"
                    id="alamat"
                    name="alamat"
                    value={requestForm.alamat}
                    onChange={handleInputChange}
                    className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-purple-500"
                    required
                  />
                </div>
                
                <div>
                  <label htmlFor="namakegiatan" className="block text-sm font-medium text-gray-700 mb-1">
                    Nama Kegiatan
                  </label>
                  <input
                    type="text"
                    id="namakegiatan"
                    name="namakegiatan"
                    value={requestForm.namakegiatan}
                    onChange={handleInputChange}
                    className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-purple-500"
                    required
                  />
                </div>
                
                <div>
                  <label htmlFor="deskripsi" className="block text-sm font-medium text-gray-700 mb-1">
                    Deskripsi
                  </label>
                  <textarea
                    id="deskripsi"
                    name="deskripsi"
                    value={requestForm.deskripsi}
                    onChange={handleInputChange}
                    rows="3"
                    className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-purple-500"
                    required
                  ></textarea>
                </div>
                
                <div>
                  <label htmlFor="tanggal" className="block text-sm font-medium text-gray-700 mb-1">
                    Tanggal Kegiatan
                  </label>
                  <input
                    type="date"
                    id="tanggal"
                    name="tanggal"
                    value={requestForm.tanggal}
                    onChange={handleInputChange}
                    className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-purple-500"
                    required
                  />
                </div>
              </div>
              
              <div className="mt-6">
                <button
                  type="submit"
                  className="w-full bg-purple-600 text-white py-2 px-4 rounded-md hover:bg-purple-700 transition duration-200 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2"
                >
                  Submit Request
                </button>
              </div>
            </form>
          </div>
        </div>
      )}
    </nav>
  );
}
import React from 'react';

const TestimonialCard = () => {
  return (
    <div className="w-full p-4 bg-gray-50 rounded-lg mt-4">
      <div className="flex items-start">
        <div className="bg-purple-100 rounded-full p-3 mr-4">
          <div className="flex items-center justify-center">
            <div className="bg-purple-800 w-2 h-6 mr-1"></div>
            <div className="bg-purple-500 w-2 h-4"></div>
          </div>
        </div>
        <div>
          <p className="font-bold text-lg">
            KHB Event bantu kami menjual 300+ tiket dalam seminggu'
          </p>
        </div>
      </div>
    </div>
  );
};

export default TestimonialCard;
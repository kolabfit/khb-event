export default function ApplicationLogo(props) {
    return (
        <img
            src="/logo/khb.png"
            alt="Logo"
            className="w-20 h-20 fill-current text-gray-500"
            {...props}
            style={{ width: '150px', height: '70px' }}></img>
    );
}

import React from 'react';
import ReactDOM from 'react-dom';

function Header() {
    return (
        <header className="p-3 bg-dark text-white">
            <div className="container">
                <div className="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
                    <a href="/" className="d-flex align-items-center mb-2 mb-lg-0 text-white text-decoration-none h2">Chronos</a>

                    <ul className="nav ml-3 col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">
                        <li><a href="#" className="nav-link px-2 text-secondary">Home</a></li>
                        <li><a href="#" className="nav-link px-2 text-white">Features</a></li>
                        <li><a href="#" className="nav-link px-2 text-white">Pricing</a></li>
                        <li><a href="#" className="nav-link px-2 text-white">FAQs</a></li>
                        <li><a href="#" className="nav-link px-2 text-white">About</a></li>
                    </ul>

                    <div className="text-end col-12 col-lg-auto ml-auto">
                        <a type="button" href="/login" className="btn btn-outline-light me-2">Login</a>
                        <a type="button"href="/register" className="btn btn-warning ml-2">Sign-up</a>
                    </div>
                </div>
            </div>
        </header>
    );
}

if (document.getElementById('header')) {
    ReactDOM.render(<Header />, document.getElementById('header'));
}

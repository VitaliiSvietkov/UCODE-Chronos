import React, {useEffect, useState} from 'react';
import ReactDOM from 'react-dom';
import Swal from "sweetalert2";

async function logout() {
    let response = await fetch('/api/auth/logout', {
        headers: {
            'Accept': 'application/json',
        },
        method: 'GET',
    });
    if (response.status === 200) {
        location.href = '/login';
    }
    let result = await response.json();
    Swal.fire({
        icon: 'error',
        title: result.message,
    });
}

function Header() {
    const [lastElement, setState] = useState(
        <div className="text-end col-12 col-lg-auto ml-auto">
            <a type="button" href="/login" className="btn btn-outline-light me-2">Login</a>
            <a type="button"href="/register" className="btn btn-warning ml-2">Sign-up</a>
        </div>
    );

    useEffect(async () => {
        let response = await fetch('/api/auth/me', {
            headers: {
                'Accept': 'application/json',
            },
            method: 'GET',
        });
        if (response.status === 200) {
            let userEntity = await response.json();
            setState(
                <div className="text-end ml-auto">
                    <span className="text-light mr-1">{userEntity.email}</span>
                    <button className="btn btn-light" role="button" onClick={logout}>Logout</button>
                </div>
            );
        }
    }, []);

    return (
        <header className="p-3 bg-dark text-white col-12">
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


                    {lastElement}
                </div>
            </div>
        </header>
    );
}

if (document.getElementById('header')) {
    ReactDOM.render(<Header/>, document.getElementById('header'));
}

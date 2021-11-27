import React from 'react';
import ReactDOM from 'react-dom';
import Swal from "sweetalert2";

function Login() {
    async function sendLoginRequest (e) {
        e.preventDefault();

        let response = await fetch('/api/auth/login?' + $('#login').serialize(), {
           headers: {
               'Access': 'application/json',
           },
            method: 'POST'
        });
        let result = await response.json();

        if (response.status !== 200) {
            Swal.fire({
                icon: 'error',
                title: result.message,
            });
        } else {
            location.href = '/me';
        }

        return false;
    }

    return (
        <form id="login" className="col-8 col-md-5 mx-auto mt-5" onSubmit={sendLoginRequest}>
            <div className="card">
                <div className="card-header text-center">Login</div>

                <div className="card-body">

                    <div className="mb-3">
                        <label htmlFor="email" className="form-label">Email address</label>
                        <input type="email" className="form-control" id="email" name="email"
                               aria-describedby="emailHelp" />
                            <div id="emailHelp" className="form-text">We'll never share your email with anyone else.
                            </div>
                    </div>
                    <div className="mb-3">
                        <label htmlFor="password" className="form-label">Password</label>
                        <input type="password" className="form-control" id="password" name="password" />
                    </div>
                    <div className="mb-3 form-check">
                        <input type="checkbox" className="form-check-input" id="remember" name="remember" />
                        <label className="form-check-label" htmlFor="remember">Remember me</label>
                    </div>
                    <div className="row pl-3">
                        <button className="btn btn-primary" type="submit">Submit</button>
                        <a href="/register" role="button" className="btn btn-primary ml-1">Register</a>
                    </div>

                </div>
            </div>
        </form>
    );
}

if (document.getElementById('loginForm')) {
    ReactDOM.render(<Login />, document.getElementById('loginForm'));
}

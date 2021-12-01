import React from 'react';
import Swal from "sweetalert2";

function runShareCalendar(calendarId) {
    Swal.fire({
        title: 'Enter a user`s email',
        input: 'email',
        inputAttributes: {
            autocapitalize: 'off'
        },
        showCancelButton: true,
        confirmButtonText: 'Share access',
        showLoaderOnConfirm: true,
        preConfirm: (login) => {
            return fetch(`/api/calendar/${calendarId}/share?email=${login}`, {
                headers: {
                    'Accept': 'application/json'
                }
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(response.statusText);
                    }
                    return response.json()
                })
                .catch(error => {
                    Swal.showValidationMessage(
                        `Request failed: ${error}`
                    )
                })
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire(
                'Access granted successfully',
                result.value.email,
                'success'
            )
        }
    })
}

function Calendar(props) {
    return (
        <>
            <div className={"row p-0 m-0"}>
                <h2>{props.calendar.name}</h2>
                <a className="ml-2 pt-1" role="button" onClick={() => runShareCalendar(props.calendar.id)}>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                         className="bi bi-share" viewBox="0 0 16 16">
                        <path
                            d="M13.5 1a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3zM11 2.5a2.5 2.5 0 1 1 .603 1.628l-6.718 3.12a2.499 2.499 0 0 1 0 1.504l6.718 3.12a2.5 2.5 0 1 1-.488.876l-6.718-3.12a2.5 2.5 0 1 1 0-3.256l6.718-3.12A2.5 2.5 0 0 1 11 2.5zm-8.5 4a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3zm11 5.5a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3z"/>
                    </svg>
                </a>
            </div>
            <h4 className="font-italic">{'owner: ' + props.calendar.owner.email}</h4>
        </>
    );
}

export default Calendar;

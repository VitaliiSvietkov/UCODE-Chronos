import React, {useEffect, useState} from 'react';
import Swal from "sweetalert2";
import { Calendar as npmCalendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid'; // a plugin!
import interactionPlugin from "@fullcalendar/interaction" // needed for dayClick
let holidays = require('date-holidays');

import { TimepickerUI } from "timepicker-ui";

let calendar = {};

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

function runEventCreate(info) {
    Swal.fire({
        title: 'Create an event for ' + info.dateStr,
        html:
            '<div id="id1" class="version-24h mb-2" >\n' +
            '<input id="starttime" type="text" class="swal2-input timepicker-ui-input" placeholder="Start time"/>\n'+
            '</div>\n' +
            '<div id="id2" class="version-24h mb-2" >\n' +
            '<input id="endtime" type="text" class="swal2-input timepicker-ui-input" placeholder="End time"/>\n'+
            '</div>',
        focusConfirm: false,
        didRender(popup) {
            const version24H1 = document.querySelector("#id1");
            const version24H1Picker = new TimepickerUI(version24H1);
            version24H1Picker.create();
            const version24H2 = document.querySelector("#id2");
            const version24H2Picker = new TimepickerUI(version24H2);
            version24H2Picker.create();
        },
        preConfirm: () => {
            return [
                document.getElementById('starttime').value,
                document.getElementById('endtime').value
            ]
        }
    }).then((result) => {
        if (result.isConfirmed) {
            console.log((new Date(result.value[0] + ' ' + info.date.getFullYear())));
            calendar.addEventSource({
               title: "test",
               start: (new Date(result.value[0] + ' ' + info.date.getFullYear())),
                color: 'blue',
            });
        }
    });
}

function loadHolidays(calendar, years) {
    fetch('https://api.geoapify.com/v1/ipinfo?apiKey=d5c61eec09cb4359b940aeb8b6ada5e6', {
        method: 'GET'
    })
        .then(function(response) { return response.json(); })
        .then(function(json) {
            let hds = [];
            let hd  = new holidays(json.country.iso_code);

            for (let i = 0; i < years.length; ++i) {
                hds.push(hd.getHolidays(years[i]));
            }
            let events = [];
            for (let i = 0; i < years.length; ++i) {
                for (let j = 0; j < hds[i].length; ++j) {
                    events.push({
                        title: hds[i][j].name,
                        start: hds[i][j].date,
                        color: 'orange'
                    });
                }
            }
            calendar.addEventSource(events);
        });
}

function Calendar(props) {
    let currentYear       = (new Date()).getFullYear();
    let years             = [currentYear - 1, currentYear, currentYear + 1];
    const [year, setYear] = useState(currentYear);

    useEffect(() => {
        calendar = new npmCalendar(document.getElementById('CalendarTable'), {
            plugins: [ dayGridPlugin, interactionPlugin ],
            initialView: 'dayGridMonth',
            height: '100%',
            datesSet: (dateInfo) => {
                let startYear = dateInfo.start.getFullYear();
                let endYear   = dateInfo.end.getFullYear();

                if (startYear === endYear) {
                    if (!years.includes(endYear + 1)) {
                        years.push(endYear + 1);
                        setYear(endYear + 1);
                    }
                    if (!years.includes(startYear - 1)) {
                        year.push(startYear - 1);
                        setYear(startYear - 1);
                    }
                }
            },
            dateClick: runEventCreate,
        });
        if (props.calendar.has_holidays) {
            loadHolidays(calendar, years);
        }
        calendar.render();
    }, []);

    useEffect(() => {
        if (props.calendar.has_holidays && !years.includes(year)) {
            loadHolidays(calendar, [year]);
        }
    }, [year]);

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
            <div id="CalendarTable" className="h-100"></div>
        </>
    );
}

export default Calendar;

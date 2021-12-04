import React, {useState, useEffect} from 'react';
import ReactDOM from 'react-dom';
import CalendarButton from "./CalendarButtoon";
import AddCalendarButton from "./AddCalendarButton";
import Calendar from "../Calendar";

function getConstructedCalendars(calendars, active, callback) {
    let result = [];
    for (let i = 0; i < calendars.length; ++i) {
        result.push(<CalendarButton
            key={i}
            index={i}
            active={i === active ? "active" : ''}
            calendar={calendars[i]}
            callback={callback}
        />);
    }
    // result.push(<AddCalendarButton
    //     key={result.length}
    //     index={result.length}
    //     active={result.length === active ? "active" : ""}
    //     callback={callback}
    // />);
    return result;
}

function CalendarSidebar() {
    const [calendars, setCalendars] = useState([
        <CalendarButton key={0} active={"active"} />,
        // <AddCalendarButton key={1} />
    ]);
    const [activeCalendarId, setActiveCalendar] = useState(0);
    const [calendarsData, setCalendarsData]     = useState([]);

    let selectCalendar = (index) => {
        setActiveCalendar(index);
    }

    useEffect(() => {
        fetch('/api/user/calendars', {
            headers: {
                'Accept': 'application/json',
            },
            method: 'GET',
        }).then(async (response) => {
            let result = await response.json();
            ReactDOM.render(<Calendar calendar={result[0]} />, document.getElementById('Calendar'));
            setCalendars(getConstructedCalendars(result, activeCalendarId, selectCalendar));
            setCalendarsData(result);
        });
    }, []);

    useEffect(() => {
        setCalendars(getConstructedCalendars(calendarsData, activeCalendarId, selectCalendar));
    }, [activeCalendarId]);

    return (
        <div className="d-flex flex-column flex-shrink-0 bg-dark h-100">
            <ul className="nav nav-pills nav-flush flex-column mb-auto text-center h-100">
                {calendars}
            </ul>
        </div>
    );
}

if (document.getElementById('CalendarSidebar')) {
    ReactDOM.render(<CalendarSidebar/>, document.getElementById('CalendarSidebar'));
}

import React from 'react';
import ReactDOM from "react-dom";
import Calendar from "../Calendar";

function CalendarButton(props) {
    let renderCalendar = () => {
        props.callback(props.index)
        ReactDOM.unmountComponentAtNode(document.getElementById('Calendar'));
        ReactDOM.render(<Calendar calendar={props.calendar} />, document.getElementById('Calendar'));
    }
    return (
        <li className="nav-item" onClick={renderCalendar}>
            <a role="button" className={`nav-link ${props.active} py-3 border-bottom rounded-0`}>
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill={props.active ? "currentColor" : 'white'} className="bi bi-calendar3" viewBox="0 0 16 16">
                    <path
                        d="M14 0H2a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zM1 3.857C1 3.384 1.448 3 2 3h12c.552 0 1 .384 1 .857v10.286c0 .473-.448.857-1 .857H2c-.552 0-1-.384-1-.857V3.857z"/>
                    <path
                        d="M6.5 7a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm-9 3a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm-9 3a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"/>
                </svg>
            </a>
        </li>
    );
}

export default CalendarButton;

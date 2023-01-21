import axios from 'axios';

const STATUS_OPEN = 1;
const STATUS_CLOSED = 2;

function closeBugReport(report) {
	const promise = axios.put(`/bug-reports/${report.id}/`, { status: STATUS_CLOSED });

	promise.then(() => {
		report.status = STATUS_CLOSED;
	});

	return promise;
}

export {
	STATUS_OPEN,
	STATUS_CLOSED,
	closeBugReport,
};
declare namespace App {
namespace Contact {
namespace Data {
export type CallData = {
id: number,
contactId: number,
outcome: App.Contact.Enums.CallOutcome,
calledAt: string,
};
export type CallOutcomeData = {
contactId: number,
outcome: App.Contact.Enums.CallOutcome,
callUrl: string,
calledAt: string,
};
export type ContactData = {
id: number,
name: string,
phone: string | null,
email: string | null,
createdAt: string,
updatedAt: string,
calls: Array<App.Contact.Data.CallData> | undefined,
};
export type GetContactsData = {
search: string | null,
};
export type UpsertContactData = {
id: number | null,
name: string,
phone: string | null,
email: string | null,
};
}
namespace Enums {
export type CallOutcome = 'connected' | 'no_answer' | 'busy' | 'voicemail' | 'failed';
}
}
namespace Data {
export type PingData = {
message: string,
app: string,
time: string,
};
}
}

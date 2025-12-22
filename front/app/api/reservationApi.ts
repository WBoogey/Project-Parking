import apiClient from "@/api/client";

export interface Reservation {
  id: string;
  userId: string;
  parkingId: string;
  startTime: string;
  endTime: string;
  status: "pending" | "confirmed" | "cancelled" | "completed";
  rateId: string | null;
  amount: number | null;
  isFree: boolean;
}

export interface CreateReservationData {
  parkingId: string;
  startTime: string;
  endTime: string;
}

export interface CreateReservationResponse {
  id: string;
  userId: string;
  parkingId: string;
  startTime: string;
  endTime: string;
  status: string;
  isFree: boolean;
  amount: number | null;
  checkoutUrl?: string;
  stripeSessionId?: string;
  paymentStatus?: string;
}

export interface Invoice {
  id: string;
  invoiceNumber: string;
  type: string;
  amount: number;
  formattedAmount: string;
  currency: string;
  status: string;
  description: string;
  issuedAt: string;
  paidAt: string | null;
}

export const reservationApi = {
  getReservations: async (): Promise<Reservation[]> => {
    const response = await apiClient.get<{ data: Reservation[] }>(
      "/reservations",
    );
    return response.data.data;
  },

  getReservation: async (id: string): Promise<Reservation> => {
    const response = await apiClient.get<{ data: Reservation }>(
      `/reservations/${id}`,
    );
    return response.data.data;
  },

  createReservation: async (
    data: CreateReservationData,
  ): Promise<CreateReservationResponse> => {
    const response = await apiClient.post<{ data: CreateReservationResponse }>(
      "/reservations",
      data,
    );
    return response.data.data;
  },

  cancelReservation: async (
    id: string,
  ): Promise<{ id: string; status: string; wasRefunded: boolean }> => {
    const response = await apiClient.delete<{
      data: { id: string; status: string; wasRefunded: boolean };
    }>(`/reservations/${id}`);
    return response.data.data;
  },

  generateInvoice: async (id: string): Promise<Invoice> => {
    const response = await apiClient.post<{ data: Invoice }>(
      `/reservations/${id}/invoice`,
    );
    return response.data.data;
  },
};

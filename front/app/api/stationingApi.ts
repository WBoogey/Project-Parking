import apiClient from "@/api/client";

export interface Stationing {
  id: string;
  userId: string;
  parkingId: string;
  startTime: string;
  endTime: string | null;
  status: "active" | "completed" | "pending_payment";
  rateId: string | null;
  amount: number | null;
  isFree: boolean;
}

export interface EnterParkingResponse {
  id: string;
  userId: string;
  parkingId: string;
  startTime: string;
  status: string;
}

export interface ExitParkingResponse {
  id: string;
  userId: string;
  parkingId: string;
  startTime: string;
  endTime: string;
  status: string;
  isFree: boolean;
  amount: number | null;
  checkoutUrl?: string;
  paymentStatus?: string;
}

export const stationingApi = {
  getStationings: async (): Promise<Stationing[]> => {
    const response = await apiClient.get<{ data: Stationing[] }>("/stationings");
    return response.data.data;
  },

  enterParking: async (parkingId: string): Promise<EnterParkingResponse> => {
    const response = await apiClient.post<{ data: EnterParkingResponse }>(
      "/stationings/enter",
      { parkingId },
    );
    return response.data.data;
  },

  exitParking: async (parkingId: string): Promise<ExitParkingResponse> => {
    const response = await apiClient.post<{ data: ExitParkingResponse }>(
      "/stationings/exit",
      { parkingId },
    );
    return response.data.data;
  },

  getActiveStationing: async (
    parkingId: string,
  ): Promise<Stationing | null> => {
    try {
      const response = await apiClient.get<{ data: Stationing }>(
        `/stationings/active/${parkingId}`,
      );
      return response.data.data;
    } catch {
      return null;
    }
  },
};


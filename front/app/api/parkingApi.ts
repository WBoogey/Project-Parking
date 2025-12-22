import apiClient from "@/api/client";
import type { Rate } from "@/types/OwnerTypes";

interface ApiParkingResponse {
  parking: {
    id: string;
    location: string;
    capacity: number;
    ownerId: string;
  };
  rates: Rate[];
}

export interface ParkingWithRates {
  id: string;
  location: string;
  capacity: number;
  ownerId: string;
  rates: Rate[];
}

export const parkingApi = {
  getParkings: async (): Promise<ParkingWithRates[]> => {
    const response = await apiClient.get<{ data: ApiParkingResponse[] }>(
      "/parkings",
    );
    return response.data.data.map((item) => ({
      id: item.parking.id,
      location: item.parking.location,
      capacity: item.parking.capacity,
      ownerId: item.parking.ownerId,
      rates: item.rates,
    }));
  },

  getParkingById: async (id: string): Promise<ParkingWithRates | undefined> => {
    try {
      const response = await apiClient.get<{ data: ApiParkingResponse }>(
        `/parkings/${id}`,
      );
      const item = response.data.data;
      return {
        id: item.parking.id,
        location: item.parking.location,
        capacity: item.parking.capacity,
        ownerId: item.parking.ownerId,
        rates: item.rates,
      };
    } catch {
      return undefined;
    }
  },
};

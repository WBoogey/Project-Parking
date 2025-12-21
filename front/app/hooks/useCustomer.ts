import { useQuery } from "@tanstack/react-query";
import { customerApi } from "@/api/customerApi";
import { queryKeys } from "@/api/queryKeyFactory";

export const useCustomerReservations = () => {
  return useQuery({
    queryKey: queryKeys.customer.reservations(),
    queryFn: customerApi.getReservations,
  });
};

export const useCustomerSubscriptions = () => {
  return useQuery({
    queryKey: queryKeys.customer.subscriptions(),
    queryFn: customerApi.getSubscriptions,
  });
};

export const useCustomerStationings = () => {
  return useQuery({
    queryKey: queryKeys.customer.stationings(),
    queryFn: customerApi.getStationings,
  });
};

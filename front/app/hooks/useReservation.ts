import { useMutation, useQueryClient } from "@tanstack/react-query";
import {
  reservationApi,
  type CreateReservationData,
} from "@/api/reservationApi";
import { queryKeys } from "@/api/queryKeyFactory";

export const useCreateReservation = () => {
  const queryClient = useQueryClient();
  return useMutation({
    mutationFn: (data: CreateReservationData) =>
      reservationApi.createReservation(data),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: queryKeys.reservation.list() });
      queryClient.invalidateQueries({
        queryKey: queryKeys.customer.reservations(),
      });
    },
  });
};

export const useCancelReservation = () => {
  const queryClient = useQueryClient();
  return useMutation({
    mutationFn: (id: string) => reservationApi.cancelReservation(id),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: queryKeys.reservation.list() });
      queryClient.invalidateQueries({
        queryKey: queryKeys.customer.reservations(),
      });
    },
  });
};

export const useGenerateInvoice = () => {
  return useMutation({
    mutationFn: (id: string) => reservationApi.generateInvoice(id),
  });
};

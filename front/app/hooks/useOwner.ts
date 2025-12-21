import { useMutation, useQuery, useQueryClient } from "@tanstack/react-query";
import { ownerApi } from "@/api/ownerApi";
import { rateApi } from "@/api/rateApi";
import { queryKeys } from "@/api/queryKeyFactory";
import { useNavigate } from "react-router";
import type { CreateRateData } from "@/types/OwnerTypes";

export const useOwnerParkings = () => {
  return useQuery({
    queryKey: queryKeys.owner.parkings(),
    queryFn: ownerApi.getParkings,
  });
};

export const useAddParking = () => {
  const queryClient = useQueryClient();
  const navigate = useNavigate();

  return useMutation({
    mutationFn: ownerApi.addParking,
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: queryKeys.owner.parkings() });
      navigate("/owner");
    },
  });
};

export const useDeleteParking = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: ownerApi.deleteParking,
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: queryKeys.owner.parkings() });
    },
  });
};

export const useOwnerRates = (parkingId: string) => {
  return useQuery({
    queryKey: queryKeys.owner.rates(parkingId),
    queryFn: () => rateApi.getRates(parkingId),
    enabled: !!parkingId,
  });
};

export const useCreateRate = (parkingId: string) => {
  const queryClient = useQueryClient();
  return useMutation({
    mutationFn: (data: CreateRateData) => rateApi.createRate(parkingId, data),
    onSuccess: () => {
      queryClient.invalidateQueries({
        queryKey: queryKeys.owner.rates(parkingId),
      });
    },
  });
};

export const useUpdateRate = (parkingId: string) => {
  const queryClient = useQueryClient();
  return useMutation({
    mutationFn: ({
      rateId,
      data,
    }: {
      rateId: string;
      data: Partial<CreateRateData>;
    }) => rateApi.updateRate(parkingId, rateId, data),
    onSuccess: () => {
      queryClient.invalidateQueries({
        queryKey: queryKeys.owner.rates(parkingId),
      });
    },
  });
};

export const useDeleteRate = (parkingId: string) => {
  const queryClient = useQueryClient();
  return useMutation({
    mutationFn: (rateId: string) => rateApi.deleteRate(parkingId, rateId),
    onSuccess: () => {
      queryClient.invalidateQueries({
        queryKey: queryKeys.owner.rates(parkingId),
      });
    },
  });
};

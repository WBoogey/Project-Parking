import { useMutation, useQueryClient } from "@tanstack/react-query";
import { authApi } from "@/api/authApi";
import { queryKeys } from "@/api/queryKeyFactory";
import { useNavigate } from "react-router";

export const useLogin = () => {
  const queryClient = useQueryClient();
  const navigate = useNavigate();

  return useMutation({
    mutationFn: authApi.signin,
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: queryKeys.auth.user() });
      navigate("/");
    },
  });
};

export const useRegister = (options?: { onSuccess?: () => void }) => {
  const queryClient = useQueryClient();
  const navigate = useNavigate();

  return useMutation({
    mutationFn: authApi.signup,
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: queryKeys.auth.user() });
      if (options?.onSuccess) {
        options.onSuccess();
      } else {
        navigate("/");
      }
    },
  });
};

export const useLogout = () => {
  const queryClient = useQueryClient();
  const navigate = useNavigate();

  return useMutation({
    mutationFn: authApi.signout,
    onSuccess: () => {
      queryClient.setQueryData(queryKeys.auth.user(), null);
      queryClient.invalidateQueries({ queryKey: queryKeys.auth.user() });
      navigate("/login");
    },
  });
};

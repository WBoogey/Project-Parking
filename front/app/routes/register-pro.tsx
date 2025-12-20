import { useNavigate } from "react-router";
import AuthForm from "@/components/organisms/AuthForm";
import { useRegister } from "@/hooks/useAuth";
import type { AuthFormData } from "@/types/AuthFormTypes";

export default function RegisterPro() {
  const navigate = useNavigate();
  const { mutate, isPending, error } = useRegister({
    onSuccess: () => navigate("/owner"),
  });

  const handleRegister = (data: AuthFormData) => {
    mutate({
      firstName: data.firstName,
      lastName: data.lastName,
      email: data.email,
      password: data.password,
      role: "owner",
      companyName: data.companyName,
      address: data.address,
      city: data.city,
    });
  };

  return (
    <div className="min-h-screen flex items-center justify-center bg-gray-50 p-4">
      <AuthForm
        mode="signup-pro"
        onSubmit={handleRegister}
        onNavigateToLogin={() => navigate("/login")}
        onModeChange={(mode) => {
          if (mode === "signup") navigate("/register");
        }}
        onBackToSite={() => navigate("/")}
        isLoading={isPending}
        error={error ? (error as Error).message : undefined}
      />
    </div>
  );
}

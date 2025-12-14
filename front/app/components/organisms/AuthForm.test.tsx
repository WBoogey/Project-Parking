import { render, screen } from "@testing-library/react";
import { describe, expect, it, vi } from "vitest";
import AuthForm from "./AuthForm";
import userEvent from "@testing-library/user-event";

const mockProps = {
  onSubmit: vi.fn(),
  onGoogleAuth: vi.fn(),
  onMicrosoftAuth: vi.fn(),
  onModeChange: vi.fn(),
  onNavigateToLogin: vi.fn(),
  onNavigateToSignup: vi.fn(),
  onBackToSite: vi.fn(),
};

describe("AuthForm", () => {
  describe("Rendering - Titles", () => {
    it.each([
      { mode: "signup" as const, expectedTitle: "Créer un compte" },
      { mode: "login" as const, expectedTitle: "Connexion" },
      {
        mode: "signup-pro" as const,
        expectedTitle: "Créer un compte professionnel",
      },
    ])(
      "should display correct title for $mode mode",
      ({ mode, expectedTitle }) => {
        // Arrange
        render(<AuthForm mode={mode} {...mockProps} />);

        // Assert
        expect(screen.getByText(expectedTitle)).toBeInTheDocument();
      },
    );
  });

  describe("Rendering - OAuth buttons", () => {
    it.each([
      { mode: "signup" as const, shouldShowOAuth: true },
      { mode: "login" as const, shouldShowOAuth: true },
      { mode: "signup-pro" as const, shouldShowOAuth: false },
    ])(
      "should $shouldShowOAuth show OAuth buttons for $mode mode",
      ({ mode, shouldShowOAuth }) => {
        // Arrange
        render(<AuthForm mode={mode} {...mockProps} />);

        // Assert
        if (shouldShowOAuth) {
          expect(screen.getByText("Continuer avec Google")).toBeInTheDocument();
          expect(
            screen.getByText("Continuer avec Microsoft"),
          ).toBeInTheDocument();
        } else {
          expect(
            screen.queryByText("Continuer avec Google"),
          ).not.toBeInTheDocument();
          expect(
            screen.queryByText("Continuer avec Microsoft"),
          ).not.toBeInTheDocument();
        }
      },
    );
  });

  describe("Rendering - Fields", () => {
    it.each([
      {
        mode: "signup" as const,
        showNameFields: true,
        showCompanyFields: false,
      },
      {
        mode: "login" as const,
        showNameFields: false,
        showCompanyFields: false,
      },
      {
        mode: "signup-pro" as const,
        showNameFields: true,
        showCompanyFields: true,
      },
    ])(
      "should display correct fields for $mode mode",
      ({ mode, showNameFields, showCompanyFields }) => {
        // Arrange
        render(<AuthForm mode={mode} {...mockProps} />);

        // Assert
        if (showNameFields) {
          expect(screen.getByLabelText(/Prénom/)).toBeInTheDocument();
          expect(screen.getByLabelText(/^Nom\*/)).toBeInTheDocument();
        } else {
          expect(screen.queryByLabelText(/Prénom/)).not.toBeInTheDocument();
        }

        if (showCompanyFields) {
          expect(
            screen.getByLabelText(/Nom de l'entreprise/),
          ).toBeInTheDocument();
          expect(screen.getByLabelText(/Adresse/)).toBeInTheDocument();
          expect(screen.getByLabelText(/Ville/)).toBeInTheDocument();
        } else {
          expect(
            screen.queryByLabelText(/Nom de l'entreprise/),
          ).not.toBeInTheDocument();
        }

        expect(screen.getByLabelText(/Email/)).toBeInTheDocument();
        expect(screen.getByLabelText(/Mot de passe/)).toBeInTheDocument();
      },
    );
  });

  describe("Rendering - Submit button labels", () => {
    it.each([
      { mode: "signup" as const, expectedLabel: "Confirmer" },
      { mode: "login" as const, expectedLabel: "Se connecter" },
      { mode: "signup-pro" as const, expectedLabel: "Créer un compte" },
    ])(
      "should display '$expectedLabel' button for $mode mode",
      ({ mode, expectedLabel }) => {
        // Arrange
        render(<AuthForm mode={mode} {...mockProps} />);

        // Assert
        expect(screen.getByText(expectedLabel)).toBeInTheDocument();
      },
    );
  });

  describe("Validation", () => {
    it("should disable submit button when required fields are empty in login mode", () => {
      // Arrange
      render(<AuthForm mode="login" {...mockProps} />);

      // Assert
      expect(screen.getByText("Se connecter")).toBeDisabled();
    });

    it("should enable submit button when required fields are filled in login mode", async () => {
      // Arrange
      render(<AuthForm mode="login" {...mockProps} />);
      const user = userEvent.setup();

      // Act
      await user.type(screen.getByLabelText(/Email/), "test@email.com");
      await user.type(
        screen.getByPlaceholderText("Votre mot de passe"),
        "password123",
      );

      // Assert
      expect(screen.getByText("Se connecter")).toBeEnabled();
    });

    it("should disable submit button when required fields are empty in signup mode", () => {
      // Arrange
      render(<AuthForm mode="signup" {...mockProps} />);

      // Assert
      expect(screen.getByText("Confirmer")).toBeDisabled();
    });

    it("should enable submit button when required fields are filled in signup mode", async () => {
      // Arrange
      render(<AuthForm mode="signup" {...mockProps} />);
      const user = userEvent.setup();

      // Act
      await user.type(screen.getByLabelText(/Prénom/), "Jean");
      await user.type(screen.getByLabelText(/^Nom/), "Dupont");

      // Assert
      expect(screen.getByText("Confirmer")).toBeEnabled();
    });
  });

  describe("Callbacks", () => {
    it("should call onGoogleAuth when Google button is clicked", async () => {
      // Arrange
      const handleGoogleAuth = vi.fn();
      render(
        <AuthForm
          mode="login"
          {...mockProps}
          onGoogleAuth={handleGoogleAuth}
        />,
      );
      const user = userEvent.setup();

      // Act
      await user.click(screen.getByText("Continuer avec Google"));

      // Assert
      expect(handleGoogleAuth).toHaveBeenCalledOnce();
    });

    it("should call onModeChange when switching to pro mode", async () => {
      // Arrange
      const handleModeChange = vi.fn();
      render(
        <AuthForm
          mode="signup"
          {...mockProps}
          onModeChange={handleModeChange}
        />,
      );
      const user = userEvent.setup();

      // Act
      await user.click(screen.getByText("Créer un compte professionnel"));

      // Assert
      expect(handleModeChange).toHaveBeenCalledWith("signup-pro");
    });

    it("should call onNavigateToLogin when clicking login link", async () => {
      // Arrange
      const handleNavigateToLogin = vi.fn();
      render(
        <AuthForm
          mode="signup"
          {...mockProps}
          onNavigateToLogin={handleNavigateToLogin}
        />,
      );
      const user = userEvent.setup();

      // Act
      await user.click(screen.getByText("Se connecter"));

      // Assert
      expect(handleNavigateToLogin).toHaveBeenCalledOnce();
    });

    it("should call onBackToSite when clicking back link", async () => {
      // Arrange
      const handleBackToSite = vi.fn();
      render(
        <AuthForm
          mode="login"
          {...mockProps}
          onBackToSite={handleBackToSite}
        />,
      );
      const user = userEvent.setup();

      // Act
      await user.click(screen.getByText("← Retour au site"));

      // Assert
      expect(handleBackToSite).toHaveBeenCalledOnce();
    });

    it("should call onSubmit with form data when submitted", async () => {
      // Arrange
      const handleSubmit = vi.fn();
      render(<AuthForm mode="login" {...mockProps} onSubmit={handleSubmit} />);
      const user = userEvent.setup();

      // Act
      await user.type(screen.getByLabelText(/Email/), "test@email.com");
      await user.type(
        screen.getByPlaceholderText("Votre mot de passe"),
        "password123",
      );
      await user.click(screen.getByText("Se connecter"));

      // Assert
      expect(handleSubmit).toHaveBeenCalledWith(
        expect.objectContaining({
          email: "test@email.com",
          password: "password123",
        }),
      );
    });
  });

  describe("Navigation links", () => {
    it.each([
      { mode: "signup" as const, expectedLink: "Se connecter" },
      { mode: "login" as const, expectedLink: "S'inscrire" },
      { mode: "signup-pro" as const, expectedLink: "Se connecter" },
    ])(
      "should display correct navigation link for $mode mode",
      ({ mode, expectedLink }) => {
        // Arrange
        render(<AuthForm mode={mode} {...mockProps} />);

        // Assert
        expect(screen.getByText(expectedLink)).toBeInTheDocument();
      },
    );
  });
});
